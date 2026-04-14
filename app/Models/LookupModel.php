<?php

namespace App\Models;

use CodeIgniter\Model;

class LookupModel extends Model
{
    protected $table            = '';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'bezeichnung',
        'sortierung',
        'aktiv',
        'created_by',
        'updated_by',
    ];

    protected $validationRules = [
        'bezeichnung' => 'required|min_length[2]|max_length[100]',
    ];

    protected $validationMessages = [
        'bezeichnung' => [
            'required'   => 'Bezeichnung ist ein Pflichtfeld.',
            'min_length' => 'Mindestens 2 Zeichen erforderlich.',
            'max_length' => 'Maximal 100 Zeichen erlaubt.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $beforeInsert = ['normalizePayload', 'injectCreatedBy', 'injectDefaultValues'];
    protected $beforeUpdate = ['normalizePayload', 'injectUpdatedBy'];

    public function forTable(string $table): self
    {
        $this->builder = null;
        $this->table   = $table;
        $this->validationRules['bezeichnung'] = "required|min_length[2]|max_length[100]|is_unique[{$table}.bezeichnung,id,{id}]";

        return $this;
    }

    public function getItems(string $status = 'all'): array
    {
        $builder = $this->builder();

        if ($status === 'active') {
            $builder->where('aktiv', 1);
        } elseif ($status === 'inactive') {
            $builder->where('aktiv', 0);
        }

        return $builder
            ->orderBy('sortierung', 'ASC')
            ->orderBy('bezeichnung', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getStats(): array
    {
        return [
            'all'      => $this->builder()->countAllResults(),
            'active'   => $this->builder()->where('aktiv', 1)->countAllResults(),
            'inactive' => $this->builder()->where('aktiv', 0)->countAllResults(),
        ];
    }

    public function toggleActive(int $id): bool
    {
        $item = $this->find($id);

        if (! $item) {
            return false;
        }

        return $this->update($id, [
            'aktiv' => (int) ! ((int) ($item['aktiv'] ?? 0)),
        ]);
    }

    public function updateSorting(array $ids): bool
    {
        if ($ids === []) {
            return true;
        }

        $this->db->transStart();

        $position = 1;

        foreach ($ids as $id) {
            $id = (int) $id;

            if ($id <= 0) {
                continue;
            }

            $this->builder()
                ->where('id', $id)
                ->update([
                    'sortierung' => $position,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => function_exists('auth') && auth()->id() ? auth()->id() : null,
                ]);

            $position++;
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    protected function normalizePayload(array $data): array
    {
        if (isset($data['data']['bezeichnung'])) {
            $data['data']['bezeichnung'] = trim((string) $data['data']['bezeichnung']);
        }

        return $data;
    }

    protected function injectCreatedBy(array $data): array
    {
        if (function_exists('auth') && auth()->id()) {
            $data['data']['created_by'] = auth()->id();
        }

        return $data;
    }

    protected function injectUpdatedBy(array $data): array
    {
        if (function_exists('auth') && auth()->id()) {
            $data['data']['updated_by'] = auth()->id();
        }

        return $data;
    }

    protected function injectDefaultValues(array $data): array
    {
        if (! array_key_exists('aktiv', $data['data'])) {
            $data['data']['aktiv'] = 1;
        }

        if (
            ! array_key_exists('sortierung', $data['data'])
            || (int) $data['data']['sortierung'] <= 0
        ) {
            $data['data']['sortierung'] = $this->getNextSortOrder();
        }

        return $data;
    }

    protected function getNextSortOrder(): int
    {
        $row = $this->builder()
            ->selectMax('sortierung', 'max_sortierung')
            ->get()
            ->getRowArray();

        return ((int) ($row['max_sortierung'] ?? 0)) + 1;
    }
}