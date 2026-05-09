<?php

namespace App\Controllers;

use App\Models\EinheitModel;
use App\Models\EinheitenartModel;
use App\Models\EinheitTagModel;
use App\Models\AusstattungsmerkmalModel;
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
        $objektId  = $this->request->getGet('objekt_id');
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
        $tagModel         = new EinheitTagModel();

        return view('einheiten/show', [
            'title'    => $einheit['bezeichnung'],
            'einheit'  => $einheit,
            'vertraege' => $mietvertragModel->getMietvertraegeMitDetails($id),
            'tags'     => $tagModel->getMerkmaleForEinheit($id),
        ]);
    }

    public function new(): string
    {
        $objektModel    = new ObjektModel();
        $einheitenartModel = new EinheitenartModel();
        $ausstattungModel  = new AusstattungsmerkmalModel();
        $objektId = $this->request->getGet('objekt_id') ? (int) $this->request->getGet('objekt_id') : null;

        return view('einheiten/form', [
            'title'          => 'Neue Einheit anlegen',
            'einheit'        => ['objekt_id' => $objektId],
            'objekte'        => $objektModel->findAll(),
            'lagen'          => (new LookupModel())->forTable('einheitenlage')->getItems('active'),
            'einheitenarten' => $einheitenartModel->getAsList(),
            'merkmal_gruppen' => $ausstattungModel->getGrouped(),
            'selected_tags'  => [],
        ]);
    }

    public function create()
    {
        $post    = $this->request->getPost();
        $tagIds  = $post['ausstattungsmerkmale'] ?? [];
        unset($post['ausstattungsmerkmale']);

        if (! $this->model->save($post)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        $einheitId = $this->model->getInsertID();
        (new EinheitTagModel())->syncTags($einheitId, $tagIds);

        return redirect()->to('/einheiten')
            ->with('success', 'Einheit erfolgreich angelegt.');
    }

    public function edit(int $id): string
    {
        $einheit = $this->model->find($id);
        if (! $einheit) {
            return view('errors/html/error_404');
        }

        $objektModel       = new ObjektModel();
        $einheitenartModel = new EinheitenartModel();
        $ausstattungModel  = new AusstattungsmerkmalModel();
        $tagModel          = new EinheitTagModel();

        return view('einheiten/form', [
            'title'           => 'Einheit bearbeiten: ' . $einheit['bezeichnung'],
            'einheit'         => $einheit,
            'objekte'         => $objektModel->findAll(),
            'lagen'           => (new LookupModel())->forTable('einheitenlage')->getItems('active'),
            'einheitenarten'  => $einheitenartModel->getAsList(),
            'merkmal_gruppen' => $ausstattungModel->getGrouped(),
            'selected_tags'   => $tagModel->getTagIdsByEinheit($id),
        ]);
    }

    public function update(int $id)
    {
        $post   = $this->request->getPost();
        $tagIds = $post['ausstattungsmerkmale'] ?? [];
        unset($post['ausstattungsmerkmale']);

        $post['id'] = $id;

        if (! $this->model->save($post)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->model->errors());
        }

        (new EinheitTagModel())->syncTags($id, $tagIds);

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