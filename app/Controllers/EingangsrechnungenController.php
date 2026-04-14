<?php

namespace App\Controllers;

use App\Models\EingangsrechnungModel;
use App\Models\ObjektModel;
use App\Models\EinheitModel;

class EingangsrechnungenController extends BaseController
{
    protected EingangsrechnungModel $model;

    public function __construct()
    {
        $this->model = new EingangsrechnungModel();
    }

    public function index(): string
    {
        $objektId  = $this->request->getGet('objekt_id');
        $einheitId = $this->request->getGet('einheit_id');

        return view('eingangsrechnungen/index', [
            'title'       => 'Eingangsrechnungen',
            'rechnungen'  => $this->model->getRechnungenMitDetails(
                $objektId  ? (int) $objektId  : null,
                $einheitId ? (int) $einheitId : null
            ),
            'ueberfaellig' => $this->model->getUeberfaelligeRechnungen(),
        ]);
    }

    public function show(int $id): string
    {
        $rechnung = $this->model->getRechnungenMitDetails();
        $rechnung = current(array_filter($rechnung, fn($r) => $r['id'] == $id));

        if (! $rechnung) {
            return view('errors/html/error_404');
        }

        return view('eingangsrechnungen/show', [
            'title'    => 'Rechnung: ' . $rechnung['rechnungsnummer'],
            'rechnung' => $rechnung,
        ]);
    }

    public function new(): string
    {
        $objektModel  = new ObjektModel();
        $einheitModel = new EinheitModel();

        return view('eingangsrechnungen/form', [
            'title'    => 'Neue Eingangsrechnung',
            'rechnung' => [
                'objekt_id'      => $this->request->getGet('objekt_id'),
                'einheit_id'     => $this->request->getGet('einheit_id'),
                'rechnungsdatum' => date('Y-m-d'),
                'steuersatz'     => '19.00',
                'status'         => 'offen',
            ],
            'objekte'  => $objektModel->findAll(),
            'einheiten' => $einheitModel->getEinheitenMitDetails(),
        ]);
    }

    public function create()
    {
        $data = $this->request->getPost();

        // Brutto automatisch berechnen
        $this->model->berechneBrutto($data);

        // Pflichtfeld: mindestens eine Zuweisung
        if (empty($data['objekt_id']) && empty($data['einheit_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['zuweisung' => 'Bitte ein Objekt oder eine Einheit zuweisen.']);
        }

        // Optional: PDF-Upload
        $datei = $this->request->getFile('rechnung_datei');
        if ($datei && $datei->isValid() && ! $datei->hasMoved()) {
            $neuerName = $datei->getRandomName();
            $datei->move(WRITEPATH . 'uploads/rechnungen', $neuerName);
            $data['datei_pfad'] = 'uploads/rechnungen/' . $neuerName;
        }

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/eingangsrechnungen')
            ->with('success', 'Rechnung ' . $data['rechnungsnummer'] . ' gespeichert.');
    }

    public function edit(int $id): string
    {
        $rechnung = $this->model->find($id);
        if (! $rechnung) {
            return view('errors/html/error_404');
        }

        $objektModel  = new ObjektModel();
        $einheitModel = new EinheitModel();

        return view('eingangsrechnungen/form', [
            'title'    => 'Rechnung bearbeiten',
            'rechnung' => $rechnung,
            'objekte'  => $objektModel->findAll(),
            'einheiten' => $einheitModel->getEinheitenMitDetails(),
        ]);
    }

    public function update(int $id)
    {
        $data       = $this->request->getPost();
        $data['id'] = $id;

        $this->model->berechneBrutto($data);

        if (! $this->model->save($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to("/eingangsrechnungen/{$id}")
            ->with('success', 'Rechnung aktualisiert.');
    }

    public function alsBezahlt(int $id)
    {
        $this->model->update($id, ['status' => 'bezahlt']);
        return redirect()->back()->with('success', 'Rechnung als bezahlt markiert.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/eingangsrechnungen')->with('success', 'Rechnung gelöscht.');
    }
}
