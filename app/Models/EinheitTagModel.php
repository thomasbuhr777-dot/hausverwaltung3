<?php

namespace App\Models;

use CodeIgniter\Model;

class EinheitTagModel extends Model
{
    protected $table            = 'einheiten_tags';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'einheit_id',
        'ausstattungsmerkmal_id',
        'erstellt_am',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = null;
    protected $deletedField  = null;

    /**
     * Gibt alle Ausstattungsmerkmale einer Einheit zurück, gruppiert nach Kategorie.
     */
    public function getMerkmaleForEinheit(int $einheitId): array
    {
        $rows = $this->db->table('einheiten_tags et')
            ->select('am.id, am.bezeichnung, am.icon, am.kategorie')
            ->join('ausstattungsmerkmale am', 'am.id = et.ausstattungsmerkmal_id')
            ->where('et.einheit_id', $einheitId)
            ->where('am.geloescht_am IS NULL')
            ->orderBy('am.kategorie', 'ASC')
            ->orderBy('am.sortierung', 'ASC')
            ->get()
            ->getResultArray();

        // Nach Kategorie gruppieren
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['kategorie']][] = $row;
        }
        return $grouped;
    }

    /**
     * Gibt die IDs aller Tags einer Einheit als flaches Array zurück.
     */
    public function getTagIdsByEinheit(int $einheitId): array
    {
        return array_column(
            $this->where('einheit_id', $einheitId)->findAll(),
            'ausstattungsmerkmal_id'
        );
    }

    /**
     * Synchronisiert die Tags einer Einheit:
     * Löscht alle alten und setzt die neuen.
     */
    public function syncTags(int $einheitId, array $merkmalIds): void
    {
        $this->where('einheit_id', $einheitId)->delete();

        $merkmalIds = array_values(array_unique(array_filter(array_map('intval', $merkmalIds))));

        if ($merkmalIds === []) {
            return;
        }

        $now  = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($merkmalIds as $mid) {
            $rows[] = [
                'einheit_id'             => $einheitId,
                'ausstattungsmerkmal_id' => $mid,
                'erstellt_am'             => $now,
            ];
        }
        $this->insertBatch($rows);
    }
}
