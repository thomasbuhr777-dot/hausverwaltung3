<?php

namespace App\Services;

use App\Models\NebenkostenabrechnungModel;

/**
 * NebenkostenberechnungService
 *
 * Übernimmt die gesamte Berechnungslogik:
 * 1. Einheiten + Vorauszahlungen aus bestehenden Daten ermitteln
 * 2. Anteile je Position und Einheit berechnen
 * 3. Ergebnisse (Nachzahlung / Guthaben) speichern
 */
class NebenkostenberechnungService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // -----------------------------------------------------------------------
    // Schritt 1: Einheiten-Vorschlag aus aktiven Mietverträgen
    // -----------------------------------------------------------------------

    /**
     * Gibt vor-befüllte Einheiten-Daten für eine neue Abrechnung zurück.
     * Liest aktive Mietverträge im Abrechnungszeitraum, Wohnfläche aus
     * einheiten, Vorauszahlungen aus zahlungen.
     */
    public function vorschlagEinheiten(int $objektId, string $von, string $bis): array
    {
        // Alle Einheiten des Objekts mit aktivem Vertrag im Zeitraum
        $einheiten = $this->db->table('einheiten e')
            ->select('e.id AS einheit_id, e.bezeichnung, e.flaeche,
                      mv.id AS mietvertrag_id,
                      CONCAT_WS(" ", mv.mieter_vorname, mv.mieter_name) AS mieter_name,
                      mv.beginn_datum, mv.ende_datum')
            ->join('mietvertraege mv',
                'mv.einheit_id = e.id
                 AND mv.status = "aktiv"
                 AND mv.deleted_at IS NULL',
                'inner')
            ->where('e.objekt_id', $objektId)
            ->where('e.deleted_at IS NULL')
            ->where('mv.beginn_datum <=', $bis)
            ->groupStart()
                ->where('mv.ende_datum IS NULL')
                ->orWhere('mv.ende_datum >=', $von)
            ->groupEnd()
            ->orderBy('e.bezeichnung', 'ASC')
            ->get()->getResultArray();

        foreach ($einheiten as &$e) {
            // Vorauszahlungen im Zeitraum summieren
            $e['vorauszahlungen_gesamt'] = 0.00;
            if ($e['mietvertrag_id']) {
                $result = $this->db->table('zahlungen')
                    ->selectSum('betrag', 'summe')
                    ->where('mietvertrag_id', $e['mietvertrag_id'])
                    ->where('typ', 'nebenkosten')
                    ->where('status', 'bezahlt')
                    ->where('datum >=', $von)
                    ->where('datum <=', $bis)
                    ->where('deleted_at IS NULL')
                    ->get()->getRowArray();

                $e['vorauszahlungen_gesamt'] = (float) ($result['summe'] ?? 0);
            }
            $e['wohnflaeche']     = (float) ($e['flaeche'] ?? 0);
            $e['personenanzahl']  = 1; // Default, manuell anpassbar
        }

        return $einheiten;
    }

    // -----------------------------------------------------------------------
    // Schritt 2: Kostenvorschlag aus Eingangsrechnungen
    // -----------------------------------------------------------------------

    /**
     * Aggregiert Eingangsrechnungen des Objekts nach Kategorie
     * und mappt sie auf NK-Kategorien + schlägt Verteilerschlüssel vor.
     */
    public function vorschlagPositionen(int $objektId, string $von, string $bis): array
    {
        // Mapping: Eingangsrechnungs-Kategorie → NK-Kategorie + Schlüssel
        $mapping = [
            'heizung'        => ['nk' => 'heizung',          'schluessel' => 'wohnflaeche'],
            'wasser'         => ['nk' => 'wasser_abwasser',  'schluessel' => 'personenanzahl'],
            'strom'          => ['nk' => 'strom_allgemein',  'schluessel' => 'wohnflaeche'],
            'versicherung'   => ['nk' => 'versicherung',     'schluessel' => 'wohnflaeche'],
            'instandhaltung' => ['nk' => 'hausmeister',      'schluessel' => 'wohnflaeche'],
            'reinigung'      => ['nk' => 'reinigung',        'schluessel' => 'gleich'],
            'verwaltung'     => ['nk' => 'verwaltung',       'schluessel' => 'wohnflaeche'],
            'nebenkosten'    => ['nk' => 'sonstige',         'schluessel' => 'wohnflaeche'],
            'sonstige'       => ['nk' => 'sonstige',         'schluessel' => 'wohnflaeche'],
        ];

        // Rechnungen auf Objekt-Ebene
        $rechnungen = $this->db->table('eingangsrechnungen er')
            ->select('er.id, er.kategorie, er.beschreibung, er.bruttobetrag,
                      er.rechnungsdatum, er.objekt_id, er.einheit_id,
                      e.objekt_id AS einheit_objekt_id')
            ->join('einheiten e', 'e.id = er.einheit_id', 'left')
            ->where('er.deleted_at IS NULL')
            ->where('er.rechnungsdatum >=', $von)
            ->where('er.rechnungsdatum <=', $bis)
            ->groupStart()
                ->where('er.objekt_id', $objektId)
                ->orWhere('e.objekt_id', $objektId)
            ->groupEnd()
            ->orderBy('er.kategorie')
            ->get()->getResultArray();

        // Nach NK-Kategorie gruppieren
        $gruppen = [];
        foreach ($rechnungen as $r) {
            $m    = $mapping[$r['kategorie']] ?? $mapping['sonstige'];
            $key  = $m['nk'];
            if (! isset($gruppen[$key])) {
                $gruppen[$key] = [
                    'kategorie'           => $key,
                    'bezeichnung'         => ucfirst(str_replace('_', ' ', $key)),
                    'gesamtbetrag'        => 0.00,
                    'verteilerschluessel' => $m['schluessel'],
                    'eingangsrechnung_ids'=> [],
                ];
            }
            $gruppen[$key]['gesamtbetrag']         += (float) $r['bruttobetrag'];
            $gruppen[$key]['eingangsrechnung_ids'][] = (int)  $r['id'];
        }

        return array_values($gruppen);
    }

    // -----------------------------------------------------------------------
    // Schritt 3: Anteile berechnen und speichern
    // -----------------------------------------------------------------------

    /**
     * Hauptberechnung: liest alle Positionen + Einheiten einer Abrechnung
     * und berechnet den Anteil jeder Einheit je Position.
     * Danach wird das Gesamtergebnis in nk_ergebnisse geschrieben.
     */
    public function berechne(int $abrechnungId): array
    {
        $log = [];

        // Einheiten der Abrechnung
        $einheiten = $this->db->table('nk_einheiten')
            ->where('abrechnung_id', $abrechnungId)
            ->get()->getResultArray();

        if (empty($einheiten)) {
            return ['error' => 'Keine Einheiten in der Abrechnung.'];
        }

        // Summen für Verteilerschlüssel
        $summen = [
            'wohnflaeche'     => array_sum(array_column($einheiten, 'wohnflaeche')),
            'personenanzahl'  => array_sum(array_column($einheiten, 'personenanzahl')),
            'gleich'          => count($einheiten),
        ];

        // Positionen
        $positionen = $this->db->table('nebenkostenpositionen')
            ->where('abrechnung_id', $abrechnungId)
            ->get()->getResultArray();

        // Alte Anteile löschen
        $posIds = array_column($positionen, 'id');
        if ($posIds) {
            $this->db->table('nk_positionen_anteile')
                ->whereIn('position_id', $posIds)
                ->delete();
        }

        // Ergebnisse pro Einheit initialisieren
        $ergebnisse = [];
        foreach ($einheiten as $e) {
            $ergebnisse[$e['id']] = [
                'nk_einheit_id'          => $e['id'],
                'kosten_gesamt'          => 0.00,
                'vorauszahlungen_gesamt' => (float) $e['vorauszahlungen_gesamt'],
                'saldo'                  => 0.00,
            ];
        }

        // Anteile berechnen
        foreach ($positionen as $pos) {
            $schluessel = $pos['verteilerschluessel'];
            $gesamt     = $pos['gesamtbetrag'];
            $basis      = $summen[$schluessel] ?? count($einheiten);

            if ($basis <= 0) {
                $log[] = "Position {$pos['bezeichnung']}: Verteilerbasis = 0, übersprungen.";
                continue;
            }

            $anteile = [];
            $summeAnteile = 0.00;

            foreach ($einheiten as $idx => $e) {
                // Anteil je nach Schlüssel
                $einheitWert = match ($schluessel) {
                    'wohnflaeche'    => (float) $e['wohnflaeche'],
                    'personenanzahl' => (int)   $e['personenanzahl'],
                    'gleich'         => 1,
                    'verbrauch'      => 0, // manuell nachzutragen
                    default          => 0,
                };

                $prozent = $basis > 0 ? ($einheitWert / $basis * 100) : 0;
                $betrag  = round($gesamt * $einheitWert / $basis, 2);

                // Rundungsfehler auf letzte Einheit addieren
                if ($idx === count($einheiten) - 1) {
                    $betrag = round($gesamt - $summeAnteile, 2);
                }

                $summeAnteile += $betrag;

                $grundlage = match ($schluessel) {
                    'wohnflaeche'    => number_format($einheitWert, 2, ',', '.') . ' m² von ' . number_format($basis, 2, ',', '.') . ' m²',
                    'personenanzahl' => $einheitWert . ' Person(en) von ' . (int) $basis,
                    'gleich'         => '1 von ' . count($einheiten) . ' Einheiten',
                    default          => '–',
                };

                $anteile[] = [
                    'position_id'          => $pos['id'],
                    'nk_einheit_id'        => $e['id'],
                    'anteil_prozent'       => round($prozent, 4),
                    'anteil_betrag'        => $betrag,
                    'berechnungsgrundlage' => $grundlage,
                    'created_at'           => date('Y-m-d H:i:s'),
                    'updated_at'           => date('Y-m-d H:i:s'),
                ];

                $ergebnisse[$e['id']]['kosten_gesamt'] += $betrag;
            }

            $this->db->table('nk_positionen_anteile')->insertBatch($anteile);
            $log[] = "Position '{$pos['bezeichnung']}': {$gesamt} € verteilt auf " . count($einheiten) . " Einheiten.";
        }

        // Saldo berechnen und nk_ergebnisse schreiben
        $this->db->table('nk_ergebnisse')
            ->whereIn('nk_einheit_id', array_column($einheiten, 'id'))
            ->delete();

        foreach ($ergebnisse as &$erg) {
            $erg['saldo']      = round($erg['kosten_gesamt'] - $erg['vorauszahlungen_gesamt'], 2);
            $erg['created_at'] = date('Y-m-d H:i:s');
            $erg['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('nk_ergebnisse')->insertBatch(array_values($ergebnisse));

        // Abrechnung auf "fertig" setzen
        $this->db->table('nebenkostenabrechnungen')
            ->where('id', $abrechnungId)
            ->update(['status' => 'fertig', 'updated_at' => date('Y-m-d H:i:s')]);

        $log[] = 'Berechnung abgeschlossen.';
        return ['success' => true, 'log' => $log];
    }

    // -----------------------------------------------------------------------
    // Ergebnisse für eine Einheit abrufen (für PDF / Anzeige)
    // -----------------------------------------------------------------------

    public function getEinheitAbrechnung(int $nkEinheitId): array
    {
        $einheit = $this->db->table('nk_einheiten e')
            ->select('e.*, ei.bezeichnung AS einheit_bezeichnung,
                      a.bezeichnung AS abrechnung_bezeichnung,
                      a.zeitraum_von, a.zeitraum_bis, a.jahr,
                      o.bezeichnung AS objekt_bezeichnung,
                      o.strasse, o.hausnummer, o.plz, o.ort,
                      er.kosten_gesamt, er.vorauszahlungen_gesamt, er.saldo')
            ->join('einheiten ei',                'ei.id = e.einheit_id')
            ->join('nebenkostenabrechnungen a',    'a.id  = e.abrechnung_id')
            ->join('objekte o',                   'o.id  = a.objekt_id')
            ->join('nk_ergebnisse er',            'er.nk_einheit_id = e.id', 'left')
            ->where('e.id', $nkEinheitId)
            ->get()->getRowArray();

        if (! $einheit) {
            return [];
        }

        // Einzelne Positionen mit Anteil
        $einheit['positionen'] = $this->db->table('nk_positionen_anteile pa')
            ->select('pa.anteil_betrag, pa.anteil_prozent, pa.berechnungsgrundlage,
                      p.bezeichnung, p.kategorie, p.gesamtbetrag, p.verteilerschluessel')
            ->join('nebenkostenpositionen p', 'p.id = pa.position_id')
            ->where('pa.nk_einheit_id', $nkEinheitId)
            ->orderBy('p.sortierung', 'ASC')
            ->get()->getResultArray();

        return $einheit;
    }
}
