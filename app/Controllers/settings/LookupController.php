<?php

namespace App\Controllers\Settings;

use App\Controllers\BaseController;
use App\Models\LookupModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class LookupController extends BaseController
{
    protected LookupModel $model;

    protected array $allowedTables = [
        'objektarten',
        'etagen',
        'einheitenarten',
        'einheitengeschoss',
        'einheitenlage',
        'ausstattungen',
        'heizungsarten',
        'energieausweis_typen',
    ];

    public function __construct()
    {
        $this->model = new LookupModel();
    }

    protected function init(string $table): LookupModel
    {
        if (! in_array($table, $this->allowedTables, true)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->model->forTable($table);
    }

    protected function makeTitle(string $table): string
    {
        return ucfirst(str_replace('_', ' ', $table));
    }

    protected function redirectToIndex(string $table): RedirectResponse
    {
        return redirect()->to(base_url("settings/lookup/{$table}"));
    }

    protected function normalizeStatusFilter(?string $status): string
    {
        return in_array($status, ['all', 'active', 'inactive'], true) ? $status : 'all';
    }

    public function index(string $table): string
    {
        $model  = $this->init($table);
        $status = $this->normalizeStatusFilter($this->request->getGet('status'));

        return view('settings/lookup/index', [
            'title'  => $this->makeTitle($table),
            'table'  => $table,
            'status' => $status,
            'items'  => $model->getItems($status),
            'stats'  => $model->getStats(),
        ]);
    }

    public function create(string $table): string
    {
        $this->init($table);

        return view('settings/lookup/form', [
            'title' => $this->makeTitle($table),
            'table' => $table,
        ]);
    }

    public function store(string $table): RedirectResponse
    {
        $model = $this->init($table);

        if (! $model->insert([
            'bezeichnung' => (string) $this->request->getPost('bezeichnung'),
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $model->errors());
        }

        return $this->redirectToIndex($table)
            ->with('success', 'Eintrag erfolgreich angelegt.');
    }

    public function edit(string $table, int $id): string
    {
        $model = $this->init($table);
        $item  = $model->find($id);

        if (! $item) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('settings/lookup/form', [
            'title' => $this->makeTitle($table),
            'table' => $table,
            'item'  => $item,
        ]);
    }

    public function update(string $table, int $id): RedirectResponse
    {
        $model = $this->init($table);

        if (! $model->find($id)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $model->update($id, [
            'bezeichnung' => (string) $this->request->getPost('bezeichnung'),
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $model->errors());
        }

        return $this->redirectToIndex($table)
            ->with('success', 'Eintrag erfolgreich aktualisiert.');
    }

    public function toggle(string $table, int $id): RedirectResponse
    {
        $model = $this->init($table);

        if (! $model->find($id)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $model->toggleActive($id)) {
            return $this->redirectToIndex($table)
                ->with('errors', ['Der Status konnte nicht geändert werden.']);
        }

        return $this->redirectToIndex($table)
            ->with('success', 'Status erfolgreich geändert.');
    }

    public function delete(string $table, int $id): RedirectResponse
    {
        $model = $this->init($table);

        if (! $model->find($id)) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $model->update($id, ['aktiv' => 0])) {
            return $this->redirectToIndex($table)
                ->with('errors', ['Der Eintrag konnte nicht deaktiviert werden.']);
        }

        return $this->redirectToIndex($table)
            ->with('success', 'Eintrag wurde deaktiviert.');
    }

    public function sort(string $table): ResponseInterface
    {
        $model = $this->init($table);
        $ids   = $this->request->getPost('ids');

        if (! is_array($ids)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'  => false,
                'message'  => 'Ungültige Sortierdaten.',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        if (! $model->updateSorting($ids)) {
            return $this->response->setStatusCode(500)->setJSON([
                'success'  => false,
                'message'  => 'Sortierung konnte nicht gespeichert werden.',
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'  => true,
            'message'  => 'Sortierung gespeichert.',
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ]);
    }
}