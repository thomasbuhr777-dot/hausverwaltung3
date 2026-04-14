<?php

namespace App\Controllers;

use App\Models\MietvertragModel;
use App\Models\EinheitModel;
use App\Models\ZahlungModel;

class MietvertraegeController extends BaseController
{
    protected MietvertragModel $model;

    public function __construct()
    {
        $this->model = new MietvertragModel();
    }

    public function index(): string
    {
        return view('mietvertraege/index', [
            'title'      => 'Mietverträge',
            'vertraege'  => $this->model->getMietvertraegeMitDetails(),
            'auslaufend' => $this->model->getAuslaufendeVertraege(90),
        ]);
    }

    public function show(int $id): string
    {
        $vertrag = $this->model->getMietvertragWithZahlungen($id);
        if (! $vertrag) {
            return view('errors/html/error_404');
        }

        return view('mietvertraege/show', [
            'title'   => 'Vertrag: ' . $vertrag['mieter_name'],
            'vertrag' => $vertrag,
        ]);
    }

    public function new(): string
    {
        $einheitModel = new EinheitModel();

        return view('mietvertraege/form', [
            'title'    => 'Neuen Mietvertrag anlegen',
            'vertrag'  => [],
            'einheiten' => $einheitModel->getVerfuegbareEinheiten(),
        ]);
    }

    public function create()
    {
        $data                    = $this->request->getPost();
        $data['vertragsnummer']  = $this->model->generiereVertragsnummer();

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        $id = $this->model->getInsertID();

        // Einheit als "vermietet" markieren
        $einheitModel = new EinheitModel();
        $einheitModel->setVermietet((int) $data['einheit_id']);

        // Optional: erste 12 Monate Zahlungen anlegen
        if ($this->request->getPost('zahlungen_erstellen') === '1') {
            $zahlungModel = new ZahlungModel();
            $zahlungModel->erstelleMonatszahlungen(
                array_merge($data, ['id' => $id]),
                12
            );
        }

        return redirect()->to("/mietvertraege/{$id}")
            ->with('success', "Mietvertrag {$data['vertragsnummer']} angelegt.");
    }

    public function edit(int $id): string
    {
        $vertrag = $this->model->find($id);
        if (! $vertrag) {
            return view('errors/html/error_404');
        }

        $einheitModel = new EinheitModel();

        return view('mietvertraege/form', [
            'title'    => 'Vertrag bearbeiten',
            'vertrag'  => $vertrag,
            'einheiten' => $einheitModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $data       = $this->request->getPost();
        $data['id'] = $id;

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to("/mietvertraege/{$id}")
            ->with('success', 'Mietvertrag aktualisiert.');
    }

    public function kuendigen(int $id)
    {
        $vertrag = $this->model->find($id);
        if (! $vertrag) {
            return redirect()->to('/mietvertraege')->with('error', 'Vertrag nicht gefunden.');
        }

        $this->model->update($id, [
            'status'     => 'gekuendigt',
            'ende_datum' => $this->request->getPost('ende_datum') ?? date('Y-m-d'),
        ]);

        // Einheit wieder freigeben
        $einheitModel = new EinheitModel();
        $einheitModel->setVerfuegbar($vertrag['einheit_id']);

        return redirect()->to("/mietvertraege/{$id}")
            ->with('success', 'Mietvertrag gekündigt. Einheit ist wieder verfügbar.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/mietvertraege')->with('success', 'Vertrag gelöscht.');
    }
}
