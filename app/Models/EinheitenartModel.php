<?php

namespace App\Models;

use CodeIgniter\Model;

class EinheitenartModel extends Model
{
    protected $table            = 'einheitenarten';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'bezeichnung',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'bezeichnung' => 'required|min_length[1]|max_length[100]',
    ];

    /**
     * Gibt alle aktiven Einheitenarten als einfaches id => bezeichnung Array zurück.
     */
    public function getAsList(): array
    {
        $rows = $this->where('deleted_at IS NULL')->orderBy('bezeichnung', 'ASC')->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['id']] = $row['bezeichnung'];
        }
        return $result;
    }
}