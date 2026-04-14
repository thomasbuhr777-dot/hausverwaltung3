<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Immo-Demo-Seeder
 * php spark db:seed ImmoSeeder
 */
class ImmoSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // ----------------------------------------------------------------
        // 1. Objekte
        // ----------------------------------------------------------------
        $objekte = [
            [
                'id'            => 1,
                'bezeichnung'   => 'Mehrfamilienhaus Lindenweg',
                'strasse'       => 'Lindenweg',
                'hausnummer'    => '12',
                'plz'           => '30175',
                'ort'           => 'Hannover',
                'baujahr'       => 1975,
                'gesamtflaeche' => 420.00,
                'beschreibung'  => '6-Parteien Haus, vollständig saniert 2018.',
                'status'        => 'aktiv',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'bezeichnung'   => 'Bürogebäude Kanalstraße',
                'strasse'       => 'Kanalstraße',
                'hausnummer'    => '3a',
                'plz'           => '30167',
                'ort'           => 'Hannover',
                'baujahr'       => 2005,
                'gesamtflaeche' => 850.00,
                'beschreibung'  => 'Modernes Bürogebäude, 3 Etagen.',
                'status'        => 'aktiv',
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];
        $this->db->table('objekte')->insertBatch($objekte);

        // ----------------------------------------------------------------
        // 2. Einheiten
        // ----------------------------------------------------------------
        $einheiten = [
            // Lindenweg – Wohnungen
            ['id' => 1, 'objekt_id' => 1, 'bezeichnung' => 'Wohnung EG links',    'typ' => 'wohnung',    'etage' => 0,  'flaeche' => 68.5,  'zimmer' => 2.5, 'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'objekt_id' => 1, 'bezeichnung' => 'Wohnung EG rechts',   'typ' => 'wohnung',    'etage' => 0,  'flaeche' => 72.0,  'zimmer' => 3.0, 'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'objekt_id' => 1, 'bezeichnung' => 'Wohnung 1.OG links',  'typ' => 'wohnung',    'etage' => 1,  'flaeche' => 68.5,  'zimmer' => 2.5, 'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'objekt_id' => 1, 'bezeichnung' => 'Wohnung 1.OG rechts', 'typ' => 'wohnung',    'etage' => 1,  'flaeche' => 72.0,  'zimmer' => 3.0, 'status' => 'verfuegbar', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'objekt_id' => 1, 'bezeichnung' => 'Stellplatz 1',        'typ' => 'stellplatz', 'etage' => null,'flaeche' => 12.5, 'zimmer' => null,'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'objekt_id' => 1, 'bezeichnung' => 'Keller 1',            'typ' => 'lager',      'etage' => -1, 'flaeche' => 8.0,   'zimmer' => null,'status' => 'verfuegbar', 'created_at' => $now, 'updated_at' => $now],
            // Kanalstraße – Büros
            ['id' => 7, 'objekt_id' => 2, 'bezeichnung' => 'Büro EG',    'typ' => 'gewerbe', 'etage' => 0, 'flaeche' => 240.0, 'zimmer' => null, 'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'objekt_id' => 2, 'bezeichnung' => 'Büro 1.OG',  'typ' => 'gewerbe', 'etage' => 1, 'flaeche' => 310.0, 'zimmer' => null, 'status' => 'vermietet',  'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'objekt_id' => 2, 'bezeichnung' => 'Büro 2.OG',  'typ' => 'gewerbe', 'etage' => 2, 'flaeche' => 280.0, 'zimmer' => null, 'status' => 'verfuegbar', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('einheiten')->insertBatch($einheiten);

        // ----------------------------------------------------------------
        // 3. Mietverträge
        // ----------------------------------------------------------------
        $vertraege = [
            ['id' => 1, 'einheit_id' => 1, 'vertragsnummer' => 'MV-2022-0001', 'mieter_name' => 'Müller',  'mieter_vorname' => 'Hans',  'mieter_email' => 'h.mueller@example.de', 'mieter_telefon' => '0511-123456', 'beginn_datum' => '2022-01-01', 'ende_datum' => null,         'kaltmiete' => 750.00, 'nebenkosten' => 180.00, 'kaution' => 2250.00, 'zahlungstag' => 1, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'einheit_id' => 2, 'vertragsnummer' => 'MV-2021-0002', 'mieter_name' => 'Schmidt', 'mieter_vorname' => 'Anna',  'mieter_email' => 'a.schmidt@example.de', 'mieter_telefon' => '0511-654321', 'beginn_datum' => '2021-06-01', 'ende_datum' => null,         'kaltmiete' => 820.00, 'nebenkosten' => 200.00, 'kaution' => 2460.00, 'zahlungstag' => 1, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'einheit_id' => 3, 'vertragsnummer' => 'MV-2023-0003', 'mieter_name' => 'Weber',   'mieter_vorname' => 'Klaus', 'mieter_email' => 'k.weber@example.de',   'mieter_telefon' => null,          'beginn_datum' => '2023-03-01', 'ende_datum' => '2025-02-28', 'kaltmiete' => 730.00, 'nebenkosten' => 175.00, 'kaution' => 2190.00, 'zahlungstag' => 3, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'einheit_id' => 5, 'vertragsnummer' => 'MV-2022-0004', 'mieter_name' => 'Müller',  'mieter_vorname' => 'Hans',  'mieter_email' => 'h.mueller@example.de', 'mieter_telefon' => null,          'beginn_datum' => '2022-01-01', 'ende_datum' => null,         'kaltmiete' => 65.00,  'nebenkosten' => 0.00,   'kaution' => 130.00,  'zahlungstag' => 1, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'einheit_id' => 7, 'vertragsnummer' => 'MV-2020-0005', 'mieter_name' => 'TechHub GmbH', 'mieter_vorname' => null, 'mieter_email' => 'info@techhub.de', 'mieter_telefon' => '0511-999888', 'beginn_datum' => '2020-01-01', 'ende_datum' => null,         'kaltmiete' => 3200.00,'nebenkosten' => 600.00, 'kaution' => 9600.00, 'zahlungstag' => 1, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'einheit_id' => 8, 'vertragsnummer' => 'MV-2019-0006', 'mieter_name' => 'Consulta AG', 'mieter_vorname' => null, 'mieter_email' => 'info@consulta.de', 'mieter_telefon' => '0511-777666', 'beginn_datum' => '2019-06-01', 'ende_datum' => null,         'kaltmiete' => 4100.00,'nebenkosten' => 800.00, 'kaution' => 12300.00,'zahlungstag' => 1, 'status' => 'aktiv',    'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('mietvertraege')->insertBatch($vertraege);

        // ----------------------------------------------------------------
        // 4. Zahlungen (letzter Monat + aktueller Monat)
        // ----------------------------------------------------------------
        $letzterMonat = date('Y-m-d', strtotime('first day of last month'));
        $dieserMonat  = date('Y-m-d', strtotime('first day of this month'));

        $zahlungen = [];
        foreach ([1 => 930.00, 2 => 1020.00, 3 => 905.00, 4 => 65.00, 5 => 3800.00, 6 => 4900.00] as $mvId => $betrag) {
            $zahlungen[] = ['mietvertrag_id' => $mvId, 'betrag' => $betrag, 'datum' => $letzterMonat, 'faellig_datum' => $letzterMonat, 'typ' => 'miete', 'zahlungsart' => 'ueberweisung', 'status' => 'bezahlt', 'referenz' => 'Miete ' . date('M Y', strtotime($letzterMonat)), 'created_at' => $now, 'updated_at' => $now];
            $zahlungen[] = ['mietvertrag_id' => $mvId, 'betrag' => $betrag, 'datum' => $dieserMonat,  'faellig_datum' => $dieserMonat,  'typ' => 'miete', 'zahlungsart' => 'ueberweisung', 'status' => 'offen',   'referenz' => 'Miete ' . date('M Y', strtotime($dieserMonat)),  'created_at' => $now, 'updated_at' => $now];
        }
        $this->db->table('zahlungen')->insertBatch($zahlungen);

        // ----------------------------------------------------------------
        // 5. Eingangsrechnungen
        // ----------------------------------------------------------------
        $rechnungen = [
            // Objekt-Rechnungen (Lindenweg)
            ['objekt_id' => 1, 'einheit_id' => null, 'rechnungsnummer' => 'HDW-2024-001', 'lieferant' => 'Handwerker Meier GmbH',    'rechnungsdatum' => date('Y-m-d', strtotime('-2 month')), 'faellig_datum' => date('Y-m-d', strtotime('-1 month')), 'nettobetrag' => 1200.00, 'steuersatz' => 19.00, 'steuerbetrag' => 228.00, 'bruttobetrag' => 1428.00, 'kategorie' => 'instandhaltung', 'status' => 'bezahlt', 'beschreibung' => 'Reparatur Dachrinne', 'created_at' => $now, 'updated_at' => $now],
            ['objekt_id' => 1, 'einheit_id' => null, 'rechnungsnummer' => 'VRS-2024-045', 'lieferant' => 'Versicherung AG',          'rechnungsdatum' => date('Y-m-01'),                       'faellig_datum' => date('Y-m-15'),                       'nettobetrag' => 840.00,  'steuersatz' => 0.00,  'steuerbetrag' => 0.00,   'bruttobetrag' => 840.00,  'kategorie' => 'versicherung',   'status' => 'offen',   'beschreibung' => 'Gebäudeversicherung Q1',  'created_at' => $now, 'updated_at' => $now],
            // Einheiten-Rechnungen
            ['objekt_id' => null, 'einheit_id' => 1, 'rechnungsnummer' => 'SAN-2024-011', 'lieferant' => 'Sanitär Müller',          'rechnungsdatum' => date('Y-m-d', strtotime('-3 weeks')), 'faellig_datum' => date('Y-m-d', strtotime('+1 week')),  'nettobetrag' => 350.00,  'steuersatz' => 19.00, 'steuerbetrag' => 66.50,  'bruttobetrag' => 416.50,  'kategorie' => 'instandhaltung', 'status' => 'offen',   'beschreibung' => 'Wasserhahn Bad repariert', 'created_at' => $now, 'updated_at' => $now],
            ['objekt_id' => null, 'einheit_id' => 7, 'rechnungsnummer' => 'REN-2024-007', 'lieferant' => 'Malerbetrieb Wagner',     'rechnungsdatum' => date('Y-m-d', strtotime('-1 month')), 'faellig_datum' => date('Y-m-d', strtotime('-2 weeks')),  'nettobetrag' => 2800.00, 'steuersatz' => 19.00, 'steuerbetrag' => 532.00, 'bruttobetrag' => 3332.00, 'kategorie' => 'renovierung',    'status' => 'bezahlt', 'beschreibung' => 'Neuanstrich Büro EG',     'created_at' => $now, 'updated_at' => $now],
            // Objekt-Rechnungen (Kanalstraße)
            ['objekt_id' => 2, 'einheit_id' => null, 'rechnungsnummer' => 'STR-2024-033', 'lieferant' => 'Stadtwerke Hannover',     'rechnungsdatum' => date('Y-m-01'),                       'faellig_datum' => date('Y-m-20'),                       'nettobetrag' => 620.00,  'steuersatz' => 19.00, 'steuerbetrag' => 117.80, 'bruttobetrag' => 737.80,  'kategorie' => 'strom',          'status' => 'offen',   'beschreibung' => 'Stromabrechnung ' . date('M Y'), 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('eingangsrechnungen')->insertBatch($rechnungen);

        echo "ImmoSeeder abgeschlossen.\n";
        echo "Objekte: 2 | Einheiten: 9 | Mietverträge: 6 | Zahlungen: 12 | Rechnungen: 5\n";
    }
}
