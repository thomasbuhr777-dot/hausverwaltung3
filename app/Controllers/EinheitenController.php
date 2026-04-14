<?php

namespace App\Controllers;

use App\Models\EinheitModel;
use App\Models\ObjektModel;
use App\Models\MietvertragModel;
use App\Models\LookupModel;

class EinheitenController extends BaseController
{
    protected EinheitModel $model;

    public function __construct()
    {
        $this->model = new EinheitModel();
    }

    public function index(): string
    {
        $objektId = $this->request->getGet('objekt_id');
        $einheiten = $this->model->getEinheitenMitDetails($objektId ? (int) $objektId : null);

        $objektModel = new ObjektModel();

        return view('einheiten/index', [
            'title'            => 'Einheiten',
            'einheiten'        => $einheiten,
            'objekte'          => $objektModel->findAll(),
            'filter_objekt_id' => $objektId,
        ]);
    }

    public function show(int $id): string
    {
        $einheit = $this->model->getEinheitenMitDetails();
        $einheit = current(array_filter($einheit, fn($e) => (int) $e['id'] === $id));

        if (! $einheit) {
            return view('errors/html/error_404');
        }

        $mietvertragModel = new MietvertragModel();

        return view('einheiten/show', [
            'title'     => $einheit['bezeichnung'],
            'einheit'   => $einheit,
            'vertraege' => $mietvertragModel->getMietvertraegeMitDetails($id),
        ]);
    }

    public function new(): string
    {
        $objektModel = new ObjektModel();
        $lookupModel = new LookupModel();
        $objektId = $this->request->getGet('objekt_id') ? (int) $this->request->getGet('objekt_id') : null;

        return view('einheiten/form', [
            'title'      => 'Neue Einheit anlegen',
            'einheit'    => ['objekt_id' => $objektId],
            'objekte'    => $objektModel->findAll(),
            'geschosse'  => $lookupModel->forTable('einheitengeschoss')->getItems('active'),
            'lagen'      => (new LookupModel())->forTable('einheitenlage')->getItems('active'),
        ]);
    }

    public function create()
    {
        if (! $this->model->save($this->request->getPost())) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        return redirect()->to('/einheiten')
            ->with('success', 'Einheit erfolgreich angelegt.');
    }

    public function edit(int $id): string
    {
        $einheit = $this->model->find($id);
        if (! $einheit) {
            return view('errors/html/error_404');
        }

        $objektModel = new ObjektModel();
        $lookupModel = new LookupModel();

        return view('einheiten/form', [
            'title'      => 'Einheit bearbeiten: ' . $einheit['bezeichnung'],
            'einheit'    => $einheit,
            'objekte'    => $objektModel->findAll(),
            'geschosse'  => $lookupModel->forTable('einheitengeschoss')->getItems('active'),
            'lagen'      => (new LookupModel())->forTable('einheitenlage')->getItems('active'),
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

        return redirect()->to("/einheiten/{$id}")
            ->with('success', 'Einheit aktualisiert.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);

        return redirect()->to('/einheiten')
            ->with('success', 'Einheit gelöscht.');
    }
}