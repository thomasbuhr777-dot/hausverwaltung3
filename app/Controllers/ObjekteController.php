<?php

/**
 * ObjekteController
 *
 * Verwaltung von Immobilienobjekten (CRUD).
 *
 * Änderungen gegenüber dem Original:
 *  - Nicht benötigte Imports entfernt (EinheitModel, ZahlungModel – werden im
 *    Controller nicht direkt instanziiert).
 *  - `monatsmiete` aus show() entfernt: Die Variable wurde an die View
 *    übergeben, dort aber nie genutzt → toter Code / unnötiger DB-Query.
 *  - `delete()`: Existenz-Prüfung vor dem Löschen ergänzt; Soft-Delete-
 *    Rückgabewert wird ausgewertet.
 *  - `_404()`: Umbenennung zu `notFound()` (führender Underscore ist ein
 *    PHP-4-Relikt und verletzt PSR-12). Response-Code wird jetzt korrekt
 *    auf 404 gesetzt statt 200.
 *  - `generiereBezeichnung()`: Doppelte Model-Instanziierung beseitigt –
 *    vorhandenes $objektartModel wird weitergereicht statt `new` im Helper.
 *  - `update()`: $data['bezeichnung'] nutzt jetzt ebenfalls das bereits
 *    geladene $objektartModel (kein zweiter DB-Query für dieselbe Tabelle).
 *  - Alle public-Methoden erhalten explizite Return-Types.
 *  - PHPDoc-Blöcke für alle public Methoden.
 */

namespace App\Controllers;

use App\Models\ObjektModel;
use App\Models\ObjektartModel;
use App\Models\AdresseModel;
use App\Models\EingangsrechnungModel;
use CodeIgniter\HTTP\RedirectResponse;

class ObjekteController extends BaseController
{
    protected ObjektModel $model;

    public function __construct()
    {
        $this->model = new ObjektModel();
    }

    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Übersichtsliste aller Objekte mit aggregierten Einheiten-Statistiken.
     */
    public function index(): string
    {
        return view('objekte/index', [
            'title'   => 'Objekte',
            'objekte' => $this->model->getObjekteMitStats(),
        ]);
    }

    /**
     * Detailansicht eines einzelnen Objekts inkl. Einheiten & Rechnungen.
     */
    public function show(int $id): string
    {
        $objekt = $this->model->getObjektWithEinheiten($id);

        if (! $objekt) {
            return $this->notFound();
        }

        $rechnungModel = new EingangsrechnungModel();

        return view('objekte/show', [
            'title'      => $objekt['bezeichnung'],
            'objekt'     => $objekt,
            'rechnungen' => $rechnungModel->getRechnungenMitDetails($id),
            'ausgaben'   => $rechnungModel->getAusgabenByObjekt($id, (int) date('Y')),
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    /**
     * Formular: Neues Objekt anlegen.
     */
    public function new(): string
    {
        $objektartModel = new ObjektartModel();

        return view('objekte/form', [
            'title'                   => 'Neues Objekt anlegen',
            'objekt'                  => [],
            'objektarten'             => $objektartModel->getDropdown(),
            'eigentuemer_anzeigename' => '',
        ]);
    }

    /**
     * Speichert ein neues Objekt.
     */
    public function create(): RedirectResponse
    {
        $data                = $this->request->getPost();
        $objektartModel      = new ObjektartModel();
        $data['bezeichnung'] = $this->generiereBezeichnung($data, $objektartModel);

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/objekte')
            ->with('success', 'Objekt erfolgreich angelegt.');
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    /**
     * Formular: Bestehendes Objekt bearbeiten.
     */
    public function edit(int $id): string
    {
        $objekt = $this->model->find($id);

        if (! $objekt) {
            return $this->notFound();
        }

        $objektartModel          = new ObjektartModel();
        $eigentuemer_anzeigename = '';

        if (! empty($objekt['eigentuemer_id'])) {
            $eigentuemer_anzeigename = (new AdresseModel())
                ->getAnzeigename((int) $objekt['eigentuemer_id']);
        }

        return view('objekte/form', [
            'title'                   => 'Objekt bearbeiten: ' . $objekt['bezeichnung'],
            'objekt'                  => $objekt,
            'objektarten'             => $objektartModel->getDropdown(),
            'eigentuemer_anzeigename' => $eigentuemer_anzeigename,
        ]);
    }

    /**
     * Speichert Änderungen an einem bestehenden Objekt.
     */
    public function update(int $id): RedirectResponse
    {
        $objekt = $this->model->find($id);

        if (! $objekt) {
            return redirect()->to('/objekte')
                ->with('error', 'Objekt nicht gefunden.');
        }

        $objektartModel      = new ObjektartModel();
        $data                = $this->request->getPost();
        $data['id']          = $id;
        $data['bezeichnung'] = $this->generiereBezeichnung($data, $objektartModel);

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to("/objekte/{$id}")
            ->with('success', 'Objekt erfolgreich aktualisiert.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    /**
     * Löscht ein Objekt (Soft-Delete via Model).
     */
    public function delete(int $id): RedirectResponse
    {
        if (! $this->model->find($id)) {
            return redirect()->to('/objekte')
                ->with('error', 'Objekt nicht gefunden.');
        }

        $this->model->delete($id);

        return redirect()->to('/objekte')
            ->with('success', 'Objekt gelöscht.');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Rendert die 404-Fehlerseite mit korrektem HTTP-Statuscode.
     *
     * Vorher: view('errors/html/error_404') lieferte HTTP 200.
     * Jetzt:  HTTP 404 wird explizit gesetzt.
     */
    private function notFound(): string
    {
        $this->response->setStatusCode(404);

        return view('errors/html/error_404');
    }

    /**
     * Generiert die Bezeichnung aus Adresse + Objektart.
     *
     * Format: "Musterstraße 12 - Mehrfamilienhaus"
     *
     * Das ObjektartModel wird hereingereicht, damit im aufrufenden Code
     * keine zusätzliche Instanz erzeugt werden muss (kein doppelter DB-Hit).
     *
     * @param array<string, mixed> $data
     */
    private function generiereBezeichnung(array $data, ObjektartModel $objektartModel): string
    {
        $strasse    = trim($data['strasse']    ?? '');
        $hausnummer = trim($data['hausnummer'] ?? '');
        $adresse    = $hausnummer !== '' ? "{$strasse} {$hausnummer}" : $strasse;

        $artLabel = '';

        if (! empty($data['objektart_id'])) {
            $art      = $objektartModel->find((int) $data['objektart_id']);
            $artLabel = $art['bezeichnung'] ?? '';
        }

        return $artLabel !== '' ? "{$adresse} - {$artLabel}" : $adresse;
    }
}
