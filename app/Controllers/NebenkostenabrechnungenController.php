<?php

namespace App\Controllers;

use App\Models\NebenkostenabrechnungModel;
use App\Models\ObjektModel;
use App\Services\NebenkostenberechnungService;

class NebenkostenabrechnungenController extends BaseController
{
    protected NebenkostenabrechnungModel   $model;
    protected NebenkostenberechnungService $service;
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->model   = new NebenkostenabrechnungModel();
        $this->service = new NebenkostenberechnungService();
        $this->db      = \Config\Database::connect();
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------
    public function index(): string
    {
        $objektId = $this->request->getGet('objekt_id') ? (int) $this->request->getGet('objekt_id') : null;
        $objektModel = new ObjektModel();

        return view('nebenkosten/index', [
            'title'       => 'Nebenkostenabrechnungen',
            'abrechnungen'=> $this->model->getMitDetails($objektId),
            'objekte'     => $objektModel->findAll(),
            'filter_objekt_id' => $objektId,
        ]);
    }

    // -----------------------------------------------------------------------
    // Detail
    // -----------------------------------------------------------------------
    public function show(int $id): string
    {
        $abrechnung = $this->model->getVollstaendig($id);
        if (! $abrechnung) {
            return view('errors/html/error_404');
        }

        return view('nebenkosten/show', [
            'title'      => $abrechnung['bezeichnung'],
            'abrechnung' => $abrechnung,
        ]);
    }

    // -----------------------------------------------------------------------
    // Schritt 1: Neues Formular + Vorschlag laden
    // -----------------------------------------------------------------------
    public function new(): string
    {
        $objektModel = new ObjektModel();

        return view('nebenkosten/form_neu', [
            'title'   => 'Neue Nebenkostenabrechnung',
            'objekte' => $objektModel->findAll(),
        ]);
    }

    // -----------------------------------------------------------------------
    // Schritt 2: Vorschau mit Einheiten + Positionen
    // GET /nebenkosten/vorschau?objekt_id=1&jahr=2024
    // -----------------------------------------------------------------------
    public function vorschau(): string
    {
        $objektId = (int) $this->request->getGet('objekt_id');
        $jahr     = (int) ($this->request->getGet('jahr') ?? date('Y') - 1);
        $von      = "{$jahr}-01-01";
        $bis      = "{$jahr}-12-31";

        if (! $objektId) {
            return redirect()->to('/nebenkosten/neu')->with('error', 'Bitte ein Objekt wählen.');
        }

        $objekt    = (new ObjektModel())->find($objektId);
        $einheiten = $this->service->vorschlagEinheiten($objektId, $von, $bis);
        $positionen= $this->service->vorschlagPositionen($objektId, $von, $bis);

        return view('nebenkosten/form_vorschau', [
            'title'     => 'Abrechnung erstellen – Vorschau',
            'objekt'    => $objekt,
            'jahr'      => $jahr,
            'von'       => $von,
            'bis'       => $bis,
            'einheiten' => $einheiten,
            'positionen'=> $positionen,
        ]);
    }

    // -----------------------------------------------------------------------
    // Schritt 3: Abrechnung aus Vorschau speichern
    // -----------------------------------------------------------------------
    public function create()
    {
        $post      = $this->request->getPost();
        $objektId  = (int) $post['objekt_id'];
        $jahr      = (int) $post['jahr'];
        $von       = $post['zeitraum_von'];
        $bis       = $post['zeitraum_bis'];

        // Kopfsatz
        $abrechnungData = [
            'objekt_id'    => $objektId,
            'bezeichnung'  => $post['bezeichnung'],
            'jahr'         => $jahr,
            'zeitraum_von' => $von,
            'zeitraum_bis' => $bis,
            'status'       => 'entwurf',
            'notizen'      => $post['notizen'] ?? null,
        ];

        if (! $this->model->save($abrechnungData)) {
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        $abrechnungId = $this->model->getInsertID();

        // Positionen speichern
        $positionen = $post['positionen'] ?? [];
        foreach ($positionen as $p) {
            if (empty($p['bezeichnung']) || ! isset($p['gesamtbetrag'])) {
                continue;
            }
            $this->db->table('nebenkostenpositionen')->insert([
                'abrechnung_id'        => $abrechnungId,
                'kategorie'            => $p['kategorie']          ?? 'sonstige',
                'bezeichnung'          => $p['bezeichnung'],
                'gesamtbetrag'         => (float) $p['gesamtbetrag'],
                'verteilerschluessel'  => $p['verteilerschluessel'] ?? 'wohnflaeche',
                'eingangsrechnung_ids' => isset($p['eingangsrechnung_ids'])
                    ? json_encode($p['eingangsrechnung_ids'])
                    : null,
                'sortierung'           => (int) ($p['sortierung'] ?? 0),
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]);
        }

        // Einheiten speichern
        $einheiten = $post['einheiten'] ?? [];
        foreach ($einheiten as $e) {
            if (empty($e['einheit_id'])) {
                continue;
            }
            $this->db->table('nk_einheiten')->insert([
                'abrechnung_id'          => $abrechnungId,
                'einheit_id'             => (int) $e['einheit_id'],
                'mietvertrag_id'         => $e['mietvertrag_id'] ? (int) $e['mietvertrag_id'] : null,
                'mieter_name'            => $e['mieter_name'] ?? '',
                'zeitraum_von'           => $von,
                'zeitraum_bis'           => $bis,
                'wohnflaeche'            => (float) ($e['wohnflaeche'] ?? 0),
                'personenanzahl'         => (int)   ($e['personenanzahl'] ?? 1),
                'vorauszahlungen_gesamt' => (float) ($e['vorauszahlungen_gesamt'] ?? 0),
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to("/nebenkosten/{$abrechnungId}")
            ->with('success', 'Abrechnung angelegt. Jetzt Berechnung starten.');
    }

    // -----------------------------------------------------------------------
    // Berechnung starten
    // POST /nebenkosten/{id}/berechnen
    // -----------------------------------------------------------------------
    public function berechnen(int $id)
    {
        $abrechnung = $this->model->find($id);
        if (! $abrechnung) {
            return redirect()->to('/nebenkosten')->with('error', 'Abrechnung nicht gefunden.');
        }

        $result = $this->service->berechne($id);

        if (isset($result['error'])) {
            return redirect()->to("/nebenkosten/{$id}")
                ->with('error', $result['error']);
        }

        return redirect()->to("/nebenkosten/{$id}")
            ->with('success', 'Berechnung erfolgreich. ' . implode(' ', $result['log']));
    }

    // -----------------------------------------------------------------------
    // Einzel-Abrechnung einer Einheit anzeigen
    // -----------------------------------------------------------------------
    public function einheitAbrechnung(int $nkEinheitId): string
    {
        $daten = $this->service->getEinheitAbrechnung($nkEinheitId);
        if (! $daten) {
            return view('errors/html/error_404');
        }

        return view('nebenkosten/einheit_abrechnung', [
            'title' => 'Abrechnung – ' . ($daten['mieter_name'] ?? ''),
            'daten' => $daten,
        ]);
    }

    // -----------------------------------------------------------------------
    // Status ändern
    // -----------------------------------------------------------------------
    public function statusAendern(int $id)
    {
        $status = $this->request->getPost('status');
        $erlaubt = ['entwurf', 'fertig', 'versendet', 'abgeschlossen'];

        if (! in_array($status, $erlaubt, true)) {
            return redirect()->back()->with('error', 'Ungültiger Status.');
        }

        $this->model->update($id, ['status' => $status]);

        return redirect()->to("/nebenkosten/{$id}")
            ->with('success', 'Status auf "' . ucfirst($status) . '" gesetzt.');
    }

    // -----------------------------------------------------------------------
    // Löschen
    // -----------------------------------------------------------------------
    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/nebenkosten')
            ->with('success', 'Abrechnung gelöscht.');
    }
}
