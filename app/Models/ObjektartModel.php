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
    protected $allowedFields  = ['bezeichnung', 'created_by', 'updated_by'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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
