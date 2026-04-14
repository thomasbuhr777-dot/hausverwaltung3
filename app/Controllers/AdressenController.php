<?php

namespace App\Controllers;

use App\Models\AdresseModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdressenController extends BaseController
{
    protected AdresseModel $model;

    public function __construct()
    {
        $this->model = new AdresseModel();
    }

    // -----------------------------------------------------------------------
    // Index: Tabelle mit Server-Side Pagination + Suche
    // -----------------------------------------------------------------------
    public function index(): string
    {
        $perPage = 20;
        $suche   = trim((string) ($this->request->getGet('q') ?? ''));
        $typ     = trim((string) ($this->request->getGet('typ') ?? ''));
        $sort    = (string) ($this->request->getGet('sort') ?? 'anzeigename');
        $dir     = strtoupper((string) ($this->request->getGet('dir') ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $erlaubteSpalten = ['anzeigename', 'kontakt_typ', 'ort', 'email', 'telefon1'];
        if (! in_array($sort, $erlaubteSpalten, true)) {
            $sort = 'anzeigename';
        }

        $builder = $this->model->builder()
            ->select("id, kontakt_typ, anrede, titel, vorname, nachname, firmenname,
                      strasse, hsnr, plz, ort, land, email, telefon1, telefon2,
                      iban, bank, umsatzsteuer_id, bemerkungen,
                      CASE
                        WHEN kontakt_typ = 'firma' THEN firmenname
                        ELSE CONCAT_WS(' ', NULLIF(titel,''), NULLIF(vorname,''), nachname)
                      END AS anzeigename")
            ->where('geloescht_am IS NULL', null, false);

        if (in_array($typ, ['person', 'firma'], true)) {
            $builder->where('kontakt_typ', $typ);
        }

        if ($suche !== '') {
            $builder->groupStart()
                ->like('nachname', $suche)
                ->orLike('vorname', $suche)
                ->orLike('firmenname', $suche)
                ->orLike('email', $suche)
                ->orLike('ort', $suche)
                ->orLike('telefon1', $suche)
            ->groupEnd();
        }

        $total = (int) $builder->countAllResults(false);
        $totalPages = max(1, (int) ceil($total / $perPage));

        $page = max(1, min((int) ($this->request->getGet('page') ?? 1), $totalPages));
        $offset = ($page - 1) * $perPage;

        $orderField = $sort === 'anzeigename' ? 'anzeigename' : $sort;

        $adressen = $builder
            ->orderBy($orderField, $dir)
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('adressen/index', [
            'title'      => 'Adressbuch',
            'adressen'   => $adressen,
            'suche'      => $suche,
            'typ'        => $typ,
            'sort'       => $sort,
            'dir'        => $dir,
            'page'       => $page,
            'perPage'    => $perPage,
            'total'      => $total,
            'totalPages' => $totalPages,
        ]);
    }

    // -----------------------------------------------------------------------
    // Detail-Ansicht
    // -----------------------------------------------------------------------
    public function show(int $id): string
    {
        $adresse = $this->model->find($id);
        if (! $adresse) {
            return view('errors/html/error_404');
        }

        return view('adressen/show', [
            'title'   => $this->model->getAnzeigename($id),
            'adresse' => $adresse,
        ]);
    }

    // -----------------------------------------------------------------------
    // Create (aus Modal heraus – POST → Redirect zurück mit Flash)
    // -----------------------------------------------------------------------
    public function create()
    {
        $data = $this->request->getPost() ?? [];

        $kontaktTypErrors = $this->validateKontaktTyp($data);
        if ($kontaktTypErrors !== []) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $kontaktTypErrors);
        }

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/adressen' . $this->queryString())
            ->with('success', 'Adresse erfolgreich angelegt.');
    }

    // -----------------------------------------------------------------------
    // Edit: Gibt Adress-JSON zurück (für Modal-Prefill per Fetch)
    // -----------------------------------------------------------------------
    public function edit(int $id): ResponseInterface
    {
        $adresse = $this->model->find($id);
        if (! $adresse) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Nicht gefunden']);
        }

        return $this->response->setJSON($adresse);
    }

    // -----------------------------------------------------------------------
    // Update (aus Modal heraus – POST → Redirect)
    // -----------------------------------------------------------------------
    public function update(int $id)
    {
        $adresse = $this->model->find($id);
        if (! $adresse) {
            return redirect()->to('/adressen')->with('error', 'Adresse nicht gefunden.');
        }

        $data       = $this->request->getPost() ?? [];
        $data['id'] = $id;

        $kontaktTypErrors = $this->validateKontaktTyp($data);
        if ($kontaktTypErrors !== []) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $kontaktTypErrors);
        }

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/adressen' . $this->queryString())
            ->with('success', 'Adresse aktualisiert.');
    }

    // -----------------------------------------------------------------------
    // Delete (Soft Delete)
    // -----------------------------------------------------------------------
    public function delete(int $id)
    {
        $this->model->delete($id);

        return redirect()->to('/adressen' . $this->queryString())
            ->with('success', 'Adresse gelöscht.');
    }

    // -----------------------------------------------------------------------
    // API: Typeahead-Suche
    // -----------------------------------------------------------------------
    public function suche(): ResponseInterface
    {
        $q = trim((string) ($this->request->getGet('q') ?? ''));

        if (mb_strlen($q) < 2) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($this->model->suche($q));
    }

    // -----------------------------------------------------------------------
    // API: Schnellanlage via Modal (JSON)
    // -----------------------------------------------------------------------
    public function schnellanlage(): ResponseInterface
    {
        $data = $this->request->getPost() ?? [];

        $kontaktTypErrors = $this->validateKontaktTyp($data);
        if ($kontaktTypErrors !== []) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => reset($kontaktTypErrors),
                'errors' => $kontaktTypErrors,
            ]);
        }

        if (! $this->model->save($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'error'  => 'Speichern fehlgeschlagen.',
                'errors' => $this->model->errors(),
            ]);
        }

        $id = (int) $this->model->getInsertID();
        $adresse = $this->model->find($id);

        if (! $adresse) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Adresse wurde gespeichert, konnte aber nicht erneut geladen werden.',
            ]);
        }

        $anzeigename = $adresse['kontakt_typ'] === 'firma'
            ? $adresse['firmenname']
            : implode(' ', array_filter([
                $adresse['titel']    ?? '',
                $adresse['vorname']  ?? '',
                $adresse['nachname'] ?? '',
            ]));

        return $this->response->setStatusCode(201)->setJSON([
            'id'          => $id,
            'anzeigename' => trim($anzeigename),
            'ort'         => $adresse['ort'] ?? '',
        ]);
    }

    // -----------------------------------------------------------------------
    // Hilfsfunktion: aktuelle Filter als Query-String erhalten
    // -----------------------------------------------------------------------
    private function queryString(): string
    {
        $params = array_filter([
            'q'    => $this->request->getPost('_q')    ?? $this->request->getGet('q')    ?? '',
            'typ'  => $this->request->getPost('_typ')  ?? $this->request->getGet('typ')  ?? '',
            'sort' => $this->request->getPost('_sort') ?? $this->request->getGet('sort') ?? '',
            'dir'  => $this->request->getPost('_dir')  ?? $this->request->getGet('dir')  ?? '',
            'page' => $this->request->getPost('_page') ?? $this->request->getGet('page') ?? '',
        ]);

        return $params ? '?' . http_build_query($params) : '';
    }

    private function validateKontaktTyp(array $data): array
    {
        $errors = [];

        $kontaktTyp = trim((string) ($data['kontakt_typ'] ?? ''));
        $nachname   = trim((string) ($data['nachname'] ?? ''));
        $firmenname = trim((string) ($data['firmenname'] ?? ''));

        if ($kontaktTyp === 'person' && $nachname === '') {
            $errors['nachname'] = 'Nachname ist bei Personen Pflichtfeld.';
        }

        if ($kontaktTyp === 'firma' && $firmenname === '') {
            $errors['firmenname'] = 'Firmenname ist bei Firmen Pflichtfeld.';
        }

        return $errors;
    }
}