<?php

namespace App\Controllers;

use App\Models\ObjektModel;
use App\Models\EinheitModel;
use App\Models\EingangsrechnungModel;
use App\Models\ZahlungModel;
use App\Models\ObjektartModel;
use App\Models\AdresseModel;

class ObjekteController extends BaseController
{
    protected ObjektModel $model;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->model = new ObjektModel();
        
        // Prüfen ob Benutzer eingeloggt ist (falls Sie Auth verwenden)
        // if (!session()->get('isLoggedIn')) {
        //     return redirect()->to('/login');
        // }
    }

    public function index(): string
    {
        $objekte = $this->model->getObjekteMitStats();
        
        // Optional: Nur Objekte anzeigen, auf die der Benutzer Zugriff hat
        // $userId = session()->get('user_id');
        // $objekte = $this->model->getObjekteByUser($userId);

        return view('objekte/index', [
            'title'   => 'Objekte',
            'objekte' => $objekte,
        ]);
    }

    public function show(int $id): string
    {
        // Prüfen ob Objekt existiert
        $objekt = $this->model->getObjektWithEinheiten($id);
        if (! $objekt) {
            return $this->_404();
        }
        
        // Prüfen ob Benutzer Zugriff hat
        // if (!$this->userHasAccess($id)) {
        //     return redirect()->to('/objekte')->with('error', 'Kein Zugriff');
        // }

        $rechnungModel = new EingangsrechnungModel();
        $zahlungModel  = new ZahlungModel();
        
        // FIX: Objekt-spezifische Einnahmen
        $monatsmiete = $zahlungModel->getMonatlicheEinnahmenByObjekt($id, (int) date('Y'));
        
        // Gesamtausgaben für das Objekt
        $ausgaben = $rechnungModel->getAusgabenByObjekt($id, (int) date('Y'));
        
        // Wirtschaftliche Kennzahlen
        $jahresnettomiete = $monatsmiete * 12;
        $reinertrag = $jahresnettomiete - ($ausgaben['gesamt'] ?? 0);

        return view('objekte/show', [
            'title'            => $objekt['bezeichnung'],
            'objekt'           => $objekt,
            'rechnungen'       => $rechnungModel->getRechnungenMitDetails($id),
            'ausgaben'         => $ausgaben,
            'monatsmiete'      => $monatsmiete,
            'jahresnettomiete' => $jahresnettomiete,
            'reinertrag'       => $reinertrag,
        ]);
    }

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

    public function create()
    {
        // CSRF Protection ist bereits aktiviert
        $data = $this->request->getPost();
        
        // Validierung
        $validationRules = [
            'strasse' => 'required|min_length[2]',
            'plz'     => 'required|min_length[3]|max_length[10]',
            'ort'     => 'required|min_length[2]',
        ];
        
        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Bezeichnung generieren
        $data['bezeichnung'] = $this->generiereBezeichnung($data);
        
        // Eigentümer validieren falls vorhanden
        if (!empty($data['eigentuemer_id'])) {
            $adresseModel = new AdresseModel();
            if (!$adresseModel->find($data['eigentuemer_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ausgewählter Eigentümer existiert nicht.');
            }
        }

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/objekte')
            ->with('success', 'Objekt erfolgreich angelegt.');
    }

    public function edit(int $id): string
    {
        $objekt = $this->model->find($id);
        if (! $objekt) {
            return $this->_404();
        }

        $objektartModel = new ObjektartModel();
        $adresseModel   = new AdresseModel();
        $eigentuemer_anzeigename = '';

        if (! empty($objekt['eigentuemer_id'])) {
            $eigentuemer_anzeigename = $adresseModel->getAnzeigename((int) $objekt['eigentuemer_id']);
        }

        return view('objekte/form', [
            'title'                   => 'Objekt bearbeiten: ' . $objekt['bezeichnung'],
            'objekt'                  => $objekt,
            'objektarten'             => $objektartModel->getDropdown(),
            'eigentuemer_anzeigename' => $eigentuemer_anzeigename,
        ]);
    }

    public function update(int $id)
    {
        $objekt = $this->model->find($id);
        if (! $objekt) {
            return $this->_404();
        }

        $data = $this->request->getPost();
        $data['id'] = $id;
        
        // Bezeichnung neu generieren
        $data['bezeichnung'] = $this->generiereBezeichnung($data);

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to("/objekte/{$id}")
            ->with('success', 'Objekt erfolgreich aktualisiert.');
    }

    public function delete(int $id)
    {
        // Prüfen ob das Objekt existiert
        $objekt = $this->model->find($id);
        if (!$objekt) {
            return redirect()->to('/objekte')
                ->with('error', 'Objekt nicht gefunden.');
        }
        
        // Prüfen ob Einheiten existieren (Soft Delete)
        $einheitModel = new EinheitModel();
        $einheiten = $einheitModel->where('objekt_id', $id)->findAll();
        
        if (!empty($einheiten)) {
            return redirect()->back()
                ->with('error', 'Objekt kann nicht gelöscht werden, da noch Einheiten existieren.');
        }
        
        // Soft Delete durchführen
        $this->model->delete($id);
        
        return redirect()->to('/objekte')
            ->with('success', 'Objekt wurde gelöscht.');
    }
    
    /**
     * AJAX-Endpoint für Typeahead (sicherer gemacht)
     */
    public function sucheEigentuemer()
    {
        // CSRF Prüfung für AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        
        $query = $this->request->getGet('q');
        if (strlen($query) < 2) {
            return $this->response->setJSON([]);
        }
        
        $adresseModel = new AdresseModel();
        $results = $adresseModel->like('anzeigename', $query)
            ->orLike('nachname', $query)
            ->orLike('firmenname', $query)
            ->limit(10)
            ->find();
            
        return $this->response->setJSON($results);
    }

    private function _404(): string
    {
        return view('errors/html/error_404');
    }
    
    /**
     * Berechtigungsprüfung (Beispiel)
     */
    private function userHasAccess(int $objektId): bool
    {
        // Hier Ihre Logik implementieren
        // z.B. Prüfen ob der Benutzer der Eigentümer ist oder Admin
        return true; // Vorübergehend
    }

    /**
     * Bezeichnung aus Adresse + Objektart generieren.
     * Format: "Musterstraße 12 - Mehrfamilienhaus"
     */
    private function generiereBezeichnung(array $data): string
    {
        $strasse    = trim($data['strasse'] ?? '');
        $hausnummer = trim($data['hausnummer'] ?? '');
        $adresse    = $hausnummer ? "{$strasse} {$hausnummer}" : $strasse;

        $artLabel = '';
        if (! empty($data['objektart_id'])) {
            $art = (new \App\Models\ObjektartModel())->find((int) $data['objektart_id']);
            $artLabel = $art['bezeichnung'] ?? '';
        }

        // Fallback wenn keine Adresse
        if (empty($adresse)) {
            $adresse = 'Neues Objekt';
        }

        return $artLabel ? "{$adresse} - {$artLabel}" : $adresse;
    }
}