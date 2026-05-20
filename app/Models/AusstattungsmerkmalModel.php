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
        'erstellt_am',
        'updated_am',
        'geloescht_am',
        'erstellt_von',
        'updated_von',
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = 'updated_am';
    protected $deletedField  = 'geloescht_am';

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
