<?php

namespace App\Models;

use CodeIgniter\Model;

class MietvertragModel extends Model
{
    protected $table      = 'mietvertraege';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'einheit_id',
        'vertragsnummer',
        'mieter_name',
        'mieter_vorname',
        'mieter_email',
        'mieter_telefon',
        'beginn_datum',
        'ende_datum',
        'kaltmiete',
        'nebenkosten',
        'kaution',
        'zahlungstag',
        'status',
        'notizen',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'einheit_id'  => 'required|integer',
        'mieter_name' => 'required|min_length[2]|max_length[255]',
        'mieter_email' => 'permit_empty|valid_email',
        'beginn_datum' => 'required|valid_date',
        'kaltmiete'   => 'required|decimal|greater_than[0]',
        'nebenkosten' => 'permit_empty|decimal',
        'kaution'     => 'permit_empty|decimal',
        'zahlungstag' => 'permit_empty|integer|greater_than[0]|less_than[29]',
        'status'      => 'in_list[aktiv,beendet,gekuendigt]',
    ];

    // -----------------------------------------------------------------------

    /**
     * Vertragsnummer automatisch generieren
     */
    public function generiereVertragsnummer(): string
    {
        $year  = date('Y');
        $count = $this->db->table('mietvertraege')
            ->where('YEAR(created_at)', $year)
            ->countAllResults();

        return sprintf('MV-%s-%04d', $year, $count + 1);
    }

    /**
     * Verträge mit Einheit- und Objekt-Daten
     */
    public function getMietvertraegeMitDetails(?int $einheitId = null): array
    {
        $builder = $this->db->table('mietvertraege mv')
            ->select('mv.*, e.bezeichnung AS einheit_bezeichnung, e.typ AS einheit_typ,
                      o.id AS objekt_id, o.bezeichnung AS objekt_bezeichnung,
                      o.strasse, o.hausnummer, o.plz, o.ort,
                      (mv.kaltmiete + mv.nebenkosten) AS warmmiete')
            ->join('einheiten e', 'e.id = mv.einheit_id', 'left')
            ->join('objekte o', 'o.id = e.objekt_id', 'left')
            ->where('mv.deleted_at IS NULL');

        if ($einheitId !== null) {
            $builder->where('mv.einheit_id', $einheitId);
        }

        return $builder->orderBy('mv.status', 'ASC')
            ->orderBy('mv.beginn_datum', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Einen Vertrag mit Zahlungsübersicht
     */
    public function getMietvertragWithZahlungen(int $id): ?array
    {
        $vertrag = $this->db->table('mietvertraege mv')
            ->select('mv.*, e.bezeichnung AS einheit_bezeichnung,
                      o.bezeichnung AS objekt_bezeichnung, o.strasse, o.ort')
            ->join('einheiten e', 'e.id = mv.einheit_id')
            ->join('objekte o', 'o.id = e.objekt_id')
            ->where('mv.id', $id)
            ->where('mv.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (! $vertrag) {
            return null;
        }

        $zahlungModel         = new ZahlungModel();
        $vertrag['zahlungen'] = $zahlungModel
            ->where('mietvertrag_id', $id)
            ->orderBy('datum', 'DESC')
            ->findAll();

        // Zahlungsstatistik
        $vertrag['gesamt_bezahlt'] = array_sum(array_map(
            fn($z) => $z['status'] === 'bezahlt' ? $z['betrag'] : 0,
            $vertrag['zahlungen']
        ));

        $vertrag['gesamt_offen'] = array_sum(array_map(
            fn($z) => in_array($z['status'], ['offen', 'ueberfaellig']) ? $z['betrag'] : 0,
            $vertrag['zahlungen']
        ));

        return $vertrag;
    }

    /**
     * Aktive Verträge die heute oder früher fällig werden
     */
    public function getAuslaufendeVertraege(int $tage = 90): array
    {
        return $this->db->table('mietvertraege mv')
            ->select('mv.*, e.bezeichnung AS einheit_bezeichnung, o.bezeichnung AS objekt_bezeichnung')
            ->join('einheiten e', 'e.id = mv.einheit_id')
            ->join('objekte o', 'o.id = e.objekt_id')
            ->where('mv.status', 'aktiv')
            ->where('mv.ende_datum IS NOT NULL')
            ->where("mv.ende_datum <= DATE_ADD(NOW(), INTERVAL {$tage} DAY)")
            ->where('mv.deleted_at IS NULL')
            ->orderBy('mv.ende_datum', 'ASC')
            ->get()
            ->getResultArray();
    }
}
