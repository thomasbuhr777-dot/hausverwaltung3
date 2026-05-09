<?php

namespace App\Models;

use CodeIgniter\Model;

class AusstattungsmerkmalModel extends Model
{
    protected $table            = 'ausstattungsmerkmale';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kategorie',
        'bezeichnung',
        'slug',
        'icon',
        'sortierung',
        'aktiv',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    public function getGrouped(): array
    {
        return $this->getGroupedByKategorie();
    }

    public function getGroupedByKategorie(): array
    {
        $merkmale = $this
            ->where('aktiv', 1)
            ->orderBy('kategorie', 'ASC')
            ->orderBy('sortierung', 'ASC')
            ->orderBy('bezeichnung', 'ASC')
            ->findAll();

        $grouped = [];

        foreach ($merkmale as $merkmal) {
            $kategorie = $merkmal['kategorie'] ?: 'Sonstiges';
            $grouped[$kategorie][] = $merkmal;
        }

        return $grouped;
    }
}