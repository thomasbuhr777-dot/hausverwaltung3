<?php

namespace App\Models;

use CodeIgniter\Model;

class AdresseModel extends Model
{
    protected $table            = 'adressen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'kontakt_typ',
        'anrede',
        'firmenname',
        'titel',
        'vorname',
        'nachname',
        'strasse',
        'hsnr',
        'plz',
        'ort',
        'land',
        'lat',
        'lon',
        'telefon1',
        'telefon2',
        'email',
        'umsatzsteuer_id',
        'iban',
        'bank',
        'bemerkungen',
        'erstellt_von',
        'updated_von',
    ];

    // Tabelle nutzt abweichende Timestamp-Feldnamen
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'erstellt_am';
    protected $updatedField  = 'updated_am';
    protected $deletedField  = 'geloescht_am';

    protected $validationRules = [
        'kontakt_typ' => 'required|in_list[person,firma]',
        'nachname'    => 'permit_empty|max_length[100]',
        'firmenname'  => 'permit_empty|max_length[150]',
        'email'       => 'permit_empty|valid_email',
        'iban'        => 'permit_empty|max_length[50]',
    ];

    protected $validationMessages = [
        'kontakt_typ' => [
            'required' => 'Bitte Person oder Firma auswählen.',
        ],
    ];

    // -----------------------------------------------------------------------
    // Typeahead-Suche
    // -----------------------------------------------------------------------
    public function suche(string $term, int $limit = 10): array
    {
        $plain = trim($term);

        if ($plain === '') {
            return [];
        }

        return $this->builder()
            ->select("id, kontakt_typ, anrede, titel, vorname, nachname,
                      firmenname, strasse, hsnr, plz, ort,
                      CASE
                        WHEN kontakt_typ = 'firma' THEN firmenname
                        ELSE CONCAT_WS(' ', NULLIF(titel,''), NULLIF(vorname,''), nachname)
                      END AS anzeigename")
            ->groupStart()
                ->like('nachname', $plain, 'after')
                ->orLike('vorname', $plain, 'after')
                ->orLike('firmenname', $plain)
                ->orLike('email', $plain)
                ->orLike('ort', $plain)
            ->groupEnd()
            ->where('geloescht_am IS NULL', null, false)
            ->orderBy('anzeigename', 'ASC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Anzeigename für eine einzelne Adresse
     */
    public function getAnzeigename(int $id): string
    {
        $row = $this->select(
            "kontakt_typ, firmenname, titel, vorname, nachname,
             CASE
               WHEN kontakt_typ = 'firma' THEN firmenname
               ELSE CONCAT_WS(' ', NULLIF(titel,''), NULLIF(vorname,''), nachname)
             END AS anzeigename"
        )->find($id);

        return $row['anzeigename'] ?? '';
    }
}