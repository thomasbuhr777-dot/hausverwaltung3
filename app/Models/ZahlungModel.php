<?php

namespace App\Models;

use CodeIgniter\Model;

class ZahlungModel extends Model
{
    protected $table      = 'zahlungen';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields  = [
        'mietvertrag_id',
        'betrag',
        'datum',
        'faellig_datum',
        'typ',
        'zahlungsart',
        'status',
        'referenz',
        'notizen',
        'erstellt_am',
        'updated_am',
        'erstellt_von',
        'updated_von',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = 'updated_am';
    protected $deletedField  = 'geloescht_am';

    protected $validationRules = [
        'mietvertrag_id' => 'required|integer',
        'betrag'         => 'required|decimal|greater_than[0]',
        'datum'          => 'required|valid_date',
        'typ'            => 'required|in_list[miete,nebenkosten,kaution,sonstige]',
        'zahlungsart'    => 'in_list[ueberweisung,lastschrift,bar,sonstige]',
        'status'         => 'in_list[offen,bezahlt,teilbezahlt,ueberfaellig,storniert]',
    ];

    // -----------------------------------------------------------------------

    /**
     * Zahlungen mit Vertrags- und Mieterdaten
     */
    public function getZahlungenMitDetails(?int $mietvertragId = null): array
    {
        $builder = $this->db->table('zahlungen z')
            ->select('z.*, mv.mieter_name, mv.mieter_vorname,
                      e.bezeichnung AS einheit_bezeichnung,
                      o.bezeichnung AS objekt_bezeichnung')
            ->join('mietvertraege mv', 'mv.id = z.mietvertrag_id AND mv.geloescht_am IS NULL', 'inner')
            ->join('einheiten e', 'e.id = mv.einheit_id AND e.geloescht_am IS NULL', 'inner')
            ->join('objekte o', 'o.id = e.objekt_id AND o.geloescht_am IS NULL', 'inner')
            ->where('z.geloescht_am IS NULL');

        if ($mietvertragId !== null) {
            $builder->where('z.mietvertrag_id', $mietvertragId);
        }

        return $builder->orderBy('z.datum', 'DESC')->get()->getResultArray();
    }

    /**
     * Überfällige Zahlungen markieren
     */
    public function markiereUeberfaellig(): int
    {
        return $this->db->table('zahlungen')
            ->where('status', 'offen')
            ->where('faellig_datum <', date('Y-m-d'))
            ->where('geloescht_am IS NULL')
            ->update(['status' => 'ueberfaellig']);
    }

    /**
     * Monatliche Einnahmen-Übersicht
     */
    public function getMonatlicheEinnahmen(int $jahr): array
    {
        return $this->db->table('zahlungen z')
            ->select('MONTH(z.datum) AS monat, YEAR(z.datum) AS jahr,
                      SUM(z.betrag) AS gesamt,
                      SUM(CASE WHEN z.typ = "miete" THEN z.betrag ELSE 0 END) AS miete,
                      SUM(CASE WHEN z.typ = "nebenkosten" THEN z.betrag ELSE 0 END) AS nebenkosten')
            ->join('mietvertraege mv', 'mv.id = z.mietvertrag_id AND mv.geloescht_am IS NULL', 'inner')
            ->join('einheiten e', 'e.id = mv.einheit_id AND e.geloescht_am IS NULL', 'inner')
            ->join('objekte o', 'o.id = e.objekt_id AND o.geloescht_am IS NULL', 'inner')
            ->where('YEAR(z.datum)', $jahr)
            ->where('z.status', 'bezahlt')
            ->where('z.geloescht_am IS NULL')
            ->groupBy('YEAR(z.datum), MONTH(z.datum)')
            ->orderBy('monat', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Monatliche Zahlungen für einen Mietvertrag automatisch erstellen
     */
    public function erstelleMonatszahlungen(array $mietvertrag, int $monate = 12): bool
    {
        $zahlungen = [];
        $startDatum = new \DateTime($mietvertrag['beginn_datum']);

        for ($i = 0; $i < $monate; $i++) {
            $datum = clone $startDatum;
            $datum->modify("+{$i} month");

            $zahlungen[] = [
                'mietvertrag_id' => $mietvertrag['id'],
                'betrag'         => $mietvertrag['kaltmiete'] + $mietvertrag['nebenkosten'],
                'datum'          => $datum->format('Y-m-d'),
                'faellig_datum'  => $datum->format('Y-m-') . $mietvertrag['zahlungstag'],
                'typ'            => 'miete',
                'status'         => 'offen',
                'referenz'       => 'Miete ' . $datum->format('M Y'),
                'erstellt_am'     => date('Y-m-d H:i:s'),
                'updated_am'     => date('Y-m-d H:i:s'),
            ];
        }

        return $this->insertBatch($zahlungen) !== false;
    }
}
