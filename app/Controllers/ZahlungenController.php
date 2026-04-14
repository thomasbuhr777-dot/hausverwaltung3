<?php

namespace App\Controllers;

use App\Models\ZahlungModel;
use App\Models\MietvertragModel;

class ZahlungenController extends BaseController
{
    protected ZahlungModel $model;

    public function __construct()
    {
        $this->model = new ZahlungModel();
    }

    public function index(): string
    {
        $mietvertragId = $this->request->getGet('mietvertrag_id');
        $this->model->markiereUeberfaellig(); // automatische Statusaktualisierung

        return view('zahlungen/index', [
            'title'     => 'Zahlungen',
            'zahlungen' => $this->model->getZahlungenMitDetails(
                $mietvertragId ? (int) $mietvertragId : null
            ),
            'jahresstatistik' => $this->model->getMonatlicheEinnahmen((int) date('Y')),
        ]);
    }

    public function new(): string
    {
        $mietvertragModel = new MietvertragModel();

        return view('zahlungen/form', [
            'title'       => 'Neue Zahlung erfassen',
            'zahlung'     => [
                'mietvertrag_id' => $this->request->getGet('mietvertrag_id'),
                'datum'          => date('Y-m-d'),
                'typ'            => 'miete',
                'status'         => 'bezahlt',
            ],
            'vertraege'   => $mietvertragModel->getMietvertraegeMitDetails(),
        ]);
    }

    public function create()
    {
        if (! $this->model->save($this->request->getPost())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        $mietvertragId = $this->request->getPost('mietvertrag_id');
        return redirect()->to("/mietvertraege/{$mietvertragId}")
            ->with('success', 'Zahlung erfasst.');
    }

    public function edit(int $id): string
    {
        $zahlung = $this->model->find($id);
        if (! $zahlung) {
            return view('errors/html/error_404');
        }

        $mietvertragModel = new MietvertragModel();

        return view('zahlungen/form', [
            'title'     => 'Zahlung bearbeiten',
            'zahlung'   => $zahlung,
            'vertraege' => $mietvertragModel->getMietvertraegeMitDetails(),
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

        return redirect()->to('/zahlungen')
            ->with('success', 'Zahlung aktualisiert.');
    }

    public function alsBezahlt(int $id)
    {
        $this->model->update($id, [
            'status' => 'bezahlt',
            'datum'  => date('Y-m-d'),
        ]);

        return redirect()->back()->with('success', 'Zahlung als bezahlt markiert.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/zahlungen')->with('success', 'Zahlung gelöscht.');
    }
}
