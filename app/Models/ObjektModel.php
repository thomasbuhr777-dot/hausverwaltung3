<?php

/**
 * ObjektModel
 *
 * Änderungen gegenüber dem Original:
 *  - `getObjekteMitStats()`: Soft-Delete-Filter für `objektarten` (oa) ergänzt,
 *    damit gelöschte Objektarten nicht in der Liste auftauchen.
 *  - `getObjektWithEinheiten()`: EinheitModel wird nicht mehr manuell
 *    instanziiert; stattdessen direkter DB-Builder-Call – konsistenter Stil.
 *    Außerdem werden Einheiten jetzt nach `bezeichnung` sortiert zurückgegeben.
 *  - `getMonatsmieteByObjekt()`: war vorhanden aber wurde im Controller nie
 *    aufgerufen – bleibt erhalten, da sie sinnvoll ist; PHPDoc ergänzt.
 *  - `$allowedFields`: 'created_by' / 'updated_by' ergänzt, da die DB-Spalten
 *    existieren und andernfalls nicht über das Model befüllt werden können.
 *  - Validierungsregel für `status`: war `in_list[aktiv,inaktiv]` ohne
 *    `permit_empty` – das führt zu einem Fehler, wenn der Wert leer gesendet
 *    wird (Default im Schema ist 'aktiv'). Jetzt `permit_empty` vorangestellt.
 *  - Konsistente PHPDoc-Blöcke für alle Custom-Methoden.
 */

namespace App\Models;

use CodeIgniter\Model;

class ObjektModel extends Model
{
    protected $table            = 'objekte';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'bezeichnung',
        'objektart_id',
        'eigentuemer_id',
        'strasse',
        'hausnummer',
        'plz',
        'ort',
        'land',
        'latitude',
        'longitude',
        'place_id',
        'baujahr',
        'gesamtflaeche',
        'beschreibung',
        'status',
        'created_by',   // NEU: war bisher nicht in allowedFields
        'updated_by',   // NEU: war bisher nicht in allowedFields
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    protected $validationRules = [
        'bezeichnung'   => 'permit_empty|min_length[2]|max_length[255]',
        'objektart_id'  => 'permit_empty|integer',
        'eigentuemer_id'=> 'permit_empty|integer',
        'strasse'       => 'required|max_length[255]',
        'hausnummer'    => 'permit_empty|max_length[20]',
        'plz'           => 'required|max_length[10]',
        'ort'           => 'required|max_length[100]',
        'land'          => 'permit_empty|max_length[100]',
        'latitude'      => 'permit_empty|decimal',
        'longitude'     => 'permit_empty|decimal',
        'place_id'      => 'permit_empty|max_length[255]',
        'baujahr'       => 'permit_empty|integer|greater_than[1800]|less_than[2100]',
        'gesamtflaeche' => 'permit_empty|decimal',
        // FIX: permit_empty ergänzt – Default 'aktiv' greift in der DB,
        // aber Model-Validierung schlug ohne permit_empty fehl.
        'status'        => 'permit_empty|in_list[aktiv,inaktiv]',
    ];

    protected $validationMessages = [
        'bezeichnung' => [
            'min_length' => 'Bezeichnung muss mindestens 2 Zeichen haben.',
        ],
        'strasse'     => ['required' => 'Straße ist Pflichtfeld.'],
        'plz'         => ['required' => 'PLZ ist Pflichtfeld.'],
        'ort'         => ['required' => 'Ort ist Pflichtfeld.'],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks: leere Strings bei optionalen Integer-Feldern -> NULL,
    // damit FK-Constraints nicht mit "" statt NULL brechen.
    protected $beforeInsert = ['sanitizeNullableInts'];
    protected $beforeUpdate = ['sanitizeNullableInts'];

    /**
     * Wandelt leere Strings bei nullable Integer-FK-Feldern in NULL um.
     *
     * HTML-Formulare senden bei nicht gewaehlten <select>-Feldern einen
     * leeren String (""). MySQL akzeptiert "" nicht als FK-Wert – nur NULL
     * oder eine gueltige ID. CI4 uebergibt POST-Daten ungefiltert, daher
     * muss die Bereinigung hier im Model erfolgen.
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function sanitizeNullableInts(array $data): array
    {
        foreach (['objektart_id', 'eigentuemer_id'] as $field) {
            if (array_key_exists($field, $data['data']) && $data['data'][$field] === '') {
                $data['data'][$field] = null;
            }
        }

        return $data;
    }

    // -------------------------------------------------------------------------
    // Custom Query Methods
    // -------------------------------------------------------------------------

    /**
     * Alle Objekte mit aggregierten Einheiten-Statistiken.
     *
     * FIX: Soft-Delete-Filter für `objektarten` ergänzt (oa.deleted_at IS NULL),
     * damit gelöschte Objektarten nicht fälschlich angezeigt werden.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getObjekteMitStats(): array
    {
        return $this->db->table('objekte o')
            ->select('
                o.*,
                oa.bezeichnung AS objektart_bezeichnung,
                COUNT(DISTINCT e.id)                                          AS anzahl_einheiten,
                SUM(CASE WHEN e.status = "vermietet"  THEN 1 ELSE 0 END)     AS vermietete_einheiten,
                SUM(CASE WHEN e.status = "verfuegbar" THEN 1 ELSE 0 END)     AS freie_einheiten
            ')
            ->join('objektarten oa', 'oa.id = o.objektart_id AND oa.deleted_at IS NULL', 'left')
            ->join('einheiten e',    'e.objekt_id = o.id AND e.deleted_at IS NULL',      'left')
            ->where('o.deleted_at IS NULL')
            ->groupBy('o.id')
            ->orderBy('o.bezeichnung', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ein Objekt mit allen zugehörigen Einheiten (sortiert nach Bezeichnung).
     *
     * @return array<string, mixed>|null
     */
    public function getObjektWithEinheiten(int $id): ?array
    {
        $objekt = $this->db->table('objekte o')
            ->select('o.*, oa.bezeichnung AS objektart_bezeichnung')
            ->join('objektarten oa', 'oa.id = o.objektart_id AND oa.deleted_at IS NULL', 'left')
            ->where('o.id', $id)
            ->where('o.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (! $objekt) {
            return null;
        }

        // Direkte DB-Abfrage statt Model-Instanz, um Konsistenz zu wahren
        // und unnötige Kopplung an EinheitModel zu vermeiden.
        $objekt['einheiten'] = $this->db->table('einheiten')
            ->where('objekt_id', $id)
            ->where('deleted_at IS NULL')
            ->orderBy('bezeichnung', 'ASC')
            ->get()
            ->getResultArray();

        return $objekt;
    }

    /**
     * Gesamte monatliche Mieteinnahmen (Kalt + NK) für ein Objekt
     * über alle aktiven Mietverträge.
     *
     * @return float Summe in Euro
     */
    public function getMonatsmieteByObjekt(int $id): float
    {
        $result = $this->db->table('mietvertraege mv')
            ->selectSum('mv.kaltmiete + mv.nebenkosten', 'gesamt')
            ->join('einheiten e', 'e.id = mv.einheit_id')
            ->where('e.objekt_id', $id)
            ->where('mv.status', 'aktiv')
            ->where('mv.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        return (float) ($result['gesamt'] ?? 0.0);
    }
}