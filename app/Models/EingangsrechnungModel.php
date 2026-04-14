<?php

namespace App\Models;

use CodeIgniter\Model;

class EingangsrechnungModel extends Model
{
    protected $table      = 'eingangsrechnungen';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'objekt_id',
        'einheit_id',
        'rechnungsnummer',
        'lieferant',
        'lieferant_steuernummer',
        'rechnungsdatum',
        'faellig_datum',
        'nettobetrag',
        'steuersatz',
        'steuerbetrag',
        'bruttobetrag',
        'kategorie',
        'status',
        'beschreibung',
        'datei_pfad',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'rechnungsnummer' => 'required|max_length[100]',
        'lieferant'       => 'required|max_length[255]',
        'rechnungsdatum'  => 'required|valid_date',
        'nettobetrag'     => 'required|decimal|greater_than[0]',
        'bruttobetrag'    => 'required|decimal|greater_than[0]',
        'kategorie'       => 'required|in_list[instandhaltung,renovierung,verwaltung,versicherung,nebenkosten,strom,wasser,heizung,reinigung,sonstige]',
        'status'          => 'in_list[offen,bezahlt,ueberfaellig,storniert]',
    ];

    // -----------------------------------------------------------------------

    /**
     * Rechnungen mit Objekt/Einheit-Kontext
     */
    public function getRechnungenMitDetails(?int $objektId = null, ?int $einheitId = null): array
    {
        $builder = $this->db->table('eingangsrechnungen er')
            ->select('er.*,
                      o.bezeichnung AS objekt_bezeichnung,
                      o.strasse AS objekt_strasse, o.ort AS objekt_ort,
                      e.bezeichnung AS einheit_bezeichnung')
            ->join('objekte o', 'o.id = er.objekt_id', 'left')
            ->join('einheiten e', 'e.id = er.einheit_id', 'left')
            ->where('er.deleted_at IS NULL');

        if ($objektId !== null) {
            $builder->where('er.objekt_id', $objektId);
        }

        if ($einheitId !== null) {
            $builder->where('er.einheit_id', $einheitId);
        }

        return $builder->orderBy('er.rechnungsdatum', 'DESC')->get()->getResultArray();
    }

    /**
     * Ausgaben pro Objekt (inkl. Einheiten-Rechnungen)
     */
    public function getAusgabenByObjekt(int $objektId, ?int $jahr = null): array
    {
        $builder = $this->db->table('eingangsrechnungen er')
            ->select('er.kategorie,
                      SUM(er.nettobetrag) AS netto_gesamt,
                      SUM(er.bruttobetrag) AS brutto_gesamt,
                      COUNT(*) AS anzahl')
            ->join('einheiten e', 'e.id = er.einheit_id', 'left')
            ->where('er.deleted_at IS NULL')
            ->groupStart()
                ->where('er.objekt_id', $objektId)
                ->orWhere('e.objekt_id', $objektId)
            ->groupEnd();

        if ($jahr !== null) {
            $builder->where('YEAR(er.rechnungsdatum)', $jahr);
        }

        return $builder->groupBy('er.kategorie')
            ->orderBy('brutto_gesamt', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Brutto automatisch berechnen vor dem Speichern
     */
    public function berechneBrutto(array &$data): void
    {
        if (isset($data['nettobetrag'], $data['steuersatz'])) {
            $data['steuerbetrag'] = round($data['nettobetrag'] * ($data['steuersatz'] / 100), 2);
            $data['bruttobetrag'] = round($data['nettobetrag'] + $data['steuerbetrag'], 2);
        }
    }

    /**
     * Überfällige Rechnungen
     */
    public function getUeberfaelligeRechnungen(): array
    {
        return $this->db->table('eingangsrechnungen er')
            ->select('er.*, o.bezeichnung AS objekt_bezeichnung, e.bezeichnung AS einheit_bezeichnung')
            ->join('objekte o', 'o.id = er.objekt_id', 'left')
            ->join('einheiten e', 'e.id = er.einheit_id', 'left')
            ->where('er.status', 'offen')
            ->where('er.faellig_datum <', date('Y-m-d'))
            ->where('er.deleted_at IS NULL')
            ->orderBy('er.faellig_datum', 'ASC')
            ->get()
            ->getResultArray();
    }
}
