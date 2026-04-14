<?php

namespace App\Models;

use CodeIgniter\Model;

class NebenkostenabrechnungModel extends Model
{
    protected $table      = 'nebenkostenabrechnungen';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'objekt_id', 'bezeichnung', 'jahr',
        'zeitraum_von', 'zeitraum_bis', 'status', 'notizen',
    ];

    protected $validationRules = [
        'objekt_id'    => 'required|integer',
        'bezeichnung'  => 'required|max_length[255]',
        'jahr'         => 'required|integer',
        'zeitraum_von' => 'required|valid_date',
        'zeitraum_bis' => 'required|valid_date',
        'status'       => 'in_list[entwurf,fertig,versendet,abgeschlossen]',
    ];

    // -----------------------------------------------------------------------

    /**
     * Alle Abrechnungen mit Objekt-Namen und Positions-Zähler
     */
    public function getMitDetails(?int $objektId = null): array
    {
        $builder = $this->db->table('nebenkostenabrechnungen a')
            ->select('a.*, o.bezeichnung AS objekt_bezeichnung,
                      COUNT(DISTINCT p.id) AS anzahl_positionen,
                      COUNT(DISTINCT e.id) AS anzahl_einheiten,
                      SUM(p.gesamtbetrag)  AS kosten_gesamt')
            ->join('objekte o',               'o.id = a.objekt_id', 'left')
            ->join('nebenkostenpositionen p', 'p.abrechnung_id = a.id', 'left')
            ->join('nk_einheiten e',          'e.abrechnung_id = a.id', 'left')
            ->where('a.deleted_at IS NULL')
            ->groupBy('a.id')
            ->orderBy('a.jahr', 'DESC')
            ->orderBy('o.bezeichnung', 'ASC');

        if ($objektId) {
            $builder->where('a.objekt_id', $objektId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Eine Abrechnung mit allen Unterebenen für die Detail-Ansicht
     */
    public function getVollstaendig(int $id): ?array
    {
        $abrechnung = $this->db->table('nebenkostenabrechnungen a')
            ->select('a.*, o.bezeichnung AS objekt_bezeichnung,
                      o.strasse, o.hausnummer, o.plz, o.ort')
            ->join('objekte o', 'o.id = a.objekt_id')
            ->where('a.id', $id)
            ->where('a.deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $abrechnung) {
            return null;
        }

        // Positionen
        $abrechnung['positionen'] = $this->db->table('nebenkostenpositionen')
            ->where('abrechnung_id', $id)
            ->orderBy('sortierung', 'ASC')
            ->orderBy('kategorie',  'ASC')
            ->get()->getResultArray();

        // Einheiten mit Ergebnissen
        $abrechnung['einheiten'] = $this->db->table('nk_einheiten e')
            ->select('e.*, ei.bezeichnung AS einheit_bezeichnung,
                      er.kosten_gesamt, er.vorauszahlungen_gesamt, er.saldo')
            ->join('einheiten ei', 'ei.id = e.einheit_id', 'left')
            ->join('nk_ergebnisse er', 'er.nk_einheit_id = e.id', 'left')
            ->where('e.abrechnung_id', $id)
            ->orderBy('ei.bezeichnung', 'ASC')
            ->get()->getResultArray();

        return $abrechnung;
    }
}
