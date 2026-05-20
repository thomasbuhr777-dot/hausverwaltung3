<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjektartModel extends Model
{
    protected $table      = 'objektarten';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['bezeichnung', 'erstellt_von', 'updated_von'];

    protected $useTimestamps = true;
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = 'updated_am';
    protected $deletedField  = 'geloescht_am';

    protected $validationRules = [
        'bezeichnung' => 'required|min_length[2]|max_length[100]|is_unique[objektarten.bezeichnung,id,{id}]',
    ];

    /**
     * Für Selectbox: liefert [id => bezeichnung]
     */
    public function getDropdown(): array
    {
        return $this->orderBy('bezeichnung', 'ASC')
                    ->findAll();
    }
}
