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
        'erstellt_von',
        'updated_von',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = 'updated_am';
    protected $deletedField  = 'geloescht_am';

    protected $validationRules = [
        'bezeichnung' => 'required|min_length[1]|max_length[100]',
    ];

    /**
     * Gibt alle aktiven Einheitenarten als einfaches id => bezeichnung Array zurück.
     */
    public function getAsList(): array
    {
        $rows = $this->where('geloescht_am IS NULL')
            ->where('aktiv', 1)
            ->orderBy('sortierung', 'ASC')
            ->orderBy('bezeichnung', 'ASC')
            ->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['id']] = $row['bezeichnung'];
        }
        return $result;
    }
}
