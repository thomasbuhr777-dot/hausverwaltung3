<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AusstattungsmerkmaleSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // Küche
            ['Küche', 'Einbauküche', 'einbaukueche', 'bi bi-cup-hot'],
            ['Küche', 'Küchenzeile', 'kuechenzeile', 'bi bi-cup-hot'],
            ['Küche', 'Offene Küche', 'offene-kueche', 'bi bi-house'],
            ['Küche', 'Separate Küche', 'separate-kueche', 'bi bi-door-closed'],
            ['Küche', 'Speisekammer', 'speisekammer', 'bi bi-box-seam'],
            ['Küche', 'Herd', 'herd', 'bi bi-fire'],
            ['Küche', 'Cerankochfeld', 'cerankochfeld', 'bi bi-grid-3x3'],
            ['Küche', 'Induktionskochfeld', 'induktionskochfeld', 'bi bi-lightning-charge'],
            ['Küche', 'Gasherd', 'gasherd', 'bi bi-fire'],
            ['Küche', 'Backofen', 'backofen', 'bi bi-fire'],
            ['Küche', 'Kühlschrank', 'kuehlschrank', 'bi bi-snow'],
            ['Küche', 'Kühl-/Gefrierkombination', 'kuehl-gefrierkombination', 'bi bi-snow'],
            ['Küche', 'Gefrierschrank', 'gefrierschrank', 'bi bi-snow2'],
            ['Küche', 'Spülmaschine', 'spuelmaschine', 'bi bi-droplet'],
            ['Küche', 'Dunstabzugshaube', 'dunstabzugshaube', 'bi bi-wind'],
            ['Küche', 'Mikrowelle', 'mikrowelle', 'bi bi-broadcast'],
            ['Küche', 'Waschmaschinenanschluss Küche', 'waschmaschinenanschluss-kueche', 'bi bi-droplet-half'],

            // Bad / Sanitär
            ['Bad / Sanitär', 'Badewanne', 'badewanne', 'bi bi-water'],
            ['Bad / Sanitär', 'Dusche', 'dusche', 'bi bi-droplet'],
            ['Bad / Sanitär', 'Ebenerdige Dusche', 'ebenerdige-dusche', 'bi bi-universal-access'],
            ['Bad / Sanitär', 'Gäste-WC', 'gaeste-wc', 'bi bi-badge-wc'],
            ['Bad / Sanitär', 'Fenster im Bad', 'fenster-im-bad', 'bi bi-window'],
            ['Bad / Sanitär', 'Handtuchheizkörper', 'handtuchheizkoerper', 'bi bi-thermometer-half'],
            ['Bad / Sanitär', 'Doppelwaschbecken', 'doppelwaschbecken', 'bi bi-droplet-fill'],
            ['Bad / Sanitär', 'Bidet', 'bidet', 'bi bi-droplet'],
            ['Bad / Sanitär', 'Urinal', 'urinal', 'bi bi-droplet'],
            ['Bad / Sanitär', 'Waschmaschinenanschluss Bad', 'waschmaschinenanschluss-bad', 'bi bi-droplet-half'],
            ['Bad / Sanitär', 'Waschmaschine vorhanden', 'waschmaschine-vorhanden', 'bi bi-arrow-repeat'],
            ['Bad / Sanitär', 'Trockner vorhanden', 'trockner-vorhanden', 'bi bi-wind'],

            // Bodenbeläge
            ['Bodenbeläge', 'Parkett', 'parkett', 'bi bi-grid'],
            ['Bodenbeläge', 'Laminat', 'laminat', 'bi bi-grid'],
            ['Bodenbeläge', 'Vinylboden', 'vinylboden', 'bi bi-grid'],
            ['Bodenbeläge', 'Designboden', 'designboden', 'bi bi-grid-1x2'],
            ['Bodenbeläge', 'Teppichboden', 'teppichboden', 'bi bi-texture'],
            ['Bodenbeläge', 'Fliesen', 'fliesen', 'bi bi-grid-3x3-gap'],
            ['Bodenbeläge', 'PVC', 'pvc', 'bi bi-grid'],
            ['Bodenbeläge', 'Dielenboden', 'dielenboden', 'bi bi-border-width'],
            ['Bodenbeläge', 'Natursteinboden', 'natursteinboden', 'bi bi-gem'],

            // Außenbereiche
            ['Außenbereiche', 'Balkon', 'balkon', 'bi bi-house-door'],
            ['Außenbereiche', 'Loggia', 'loggia', 'bi bi-house-door'],
            ['Außenbereiche', 'Terrasse', 'terrasse', 'bi bi-bricks'],
            ['Außenbereiche', 'Dachterrasse', 'dachterrasse', 'bi bi-building-up'],
            ['Außenbereiche', 'Garten', 'garten', 'bi bi-tree'],
            ['Außenbereiche', 'Gartennutzung', 'gartennutzung', 'bi bi-tree-fill'],
            ['Außenbereiche', 'Wintergarten', 'wintergarten', 'bi bi-sun'],
            ['Außenbereiche', 'Gemeinschaftsgarten', 'gemeinschaftsgarten', 'bi bi-people'],

            // Heizung / Klima
            ['Heizung / Klima', 'Zentralheizung', 'zentralheizung', 'bi bi-thermometer-half'],
            ['Heizung / Klima', 'Fußbodenheizung', 'fussbodenheizung', 'bi bi-thermometer-sun'],
            ['Heizung / Klima', 'Gasheizung', 'gasheizung', 'bi bi-fire'],
            ['Heizung / Klima', 'Fernwärme', 'fernwaerme', 'bi bi-thermometer'],
            ['Heizung / Klima', 'Wärmepumpe', 'waermepumpe', 'bi bi-arrow-repeat'],
            ['Heizung / Klima', 'Kamin', 'kamin', 'bi bi-fire'],
            ['Heizung / Klima', 'Klimaanlage', 'klimaanlage', 'bi bi-snow'],
            ['Heizung / Klima', 'Wohnraumlüftung', 'wohnraumlueftung', 'bi bi-wind'],

            // Fenster / Sonnenschutz
            ['Fenster / Sonnenschutz', 'Rollläden', 'rolllaeden', 'bi bi-window'],
            ['Fenster / Sonnenschutz', 'Elektrische Rollläden', 'elektrische-rolllaeden', 'bi bi-lightning'],
            ['Fenster / Sonnenschutz', 'Außenjalousien', 'aussenjalousien', 'bi bi-window-sidebar'],
            ['Fenster / Sonnenschutz', 'Dreifachverglasung', 'dreifachverglasung', 'bi bi-window-stack'],
            ['Fenster / Sonnenschutz', 'Schallschutzfenster', 'schallschutzfenster', 'bi bi-volume-mute'],
            ['Fenster / Sonnenschutz', 'Fliegengitter', 'fliegengitter', 'bi bi-grid-3x3'],

            // Sicherheit / Zugang
            ['Sicherheit / Zugang', 'Gegensprechanlage', 'gegensprechanlage', 'bi bi-telephone'],
            ['Sicherheit / Zugang', 'Video-Gegensprechanlage', 'video-gegensprechanlage', 'bi bi-camera-video'],
            ['Sicherheit / Zugang', 'Alarmanlage', 'alarmanlage', 'bi bi-shield-lock'],
            ['Sicherheit / Zugang', 'Sicherheitstür', 'sicherheitstuer', 'bi bi-door-closed'],
            ['Sicherheit / Zugang', 'Rauchmelder', 'rauchmelder', 'bi bi-bell'],
            ['Sicherheit / Zugang', 'Smart-Home-System', 'smart-home-system', 'bi bi-house-gear'],

            // Medien / Kommunikation
            ['Medien / Kommunikation', 'Kabelanschluss', 'kabelanschluss', 'bi bi-tv'],
            ['Medien / Kommunikation', 'Satellitenanschluss', 'satellitenanschluss', 'bi bi-broadcast-pin'],
            ['Medien / Kommunikation', 'Glasfaseranschluss', 'glasfaseranschluss', 'bi bi-router'],
            ['Medien / Kommunikation', 'DSL-Anschluss', 'dsl-anschluss', 'bi bi-router'],
            ['Medien / Kommunikation', 'WLAN vorhanden', 'wlan-vorhanden', 'bi bi-wifi'],
            ['Medien / Kommunikation', 'Netzwerkverkabelung', 'netzwerkverkabelung', 'bi bi-ethernet'],

            // Gebäude / Allgemein
            ['Gebäude / Allgemein', 'Aufzug', 'aufzug', 'bi bi-arrow-up-square'],
            ['Gebäude / Allgemein', 'Barrierefrei', 'barrierefrei', 'bi bi-universal-access'],
            ['Gebäude / Allgemein', 'Seniorengerecht', 'seniorengerecht', 'bi bi-person-check'],
            ['Gebäude / Allgemein', 'Behindertengerecht', 'behindertengerecht', 'bi bi-universal-access-circle'],
            ['Gebäude / Allgemein', 'Neubau', 'neubau', 'bi bi-building'],
            ['Gebäude / Allgemein', 'Denkmalgeschützt', 'denkmalgeschuetzt', 'bi bi-bank'],
            ['Gebäude / Allgemein', 'Keller', 'keller', 'bi bi-box'],
            ['Gebäude / Allgemein', 'Abstellraum', 'abstellraum', 'bi bi-box-seam'],
            ['Gebäude / Allgemein', 'Fahrradkeller', 'fahrradkeller', 'bi bi-bicycle'],
            ['Gebäude / Allgemein', 'Trockenraum', 'trockenraum', 'bi bi-wind'],
            ['Gebäude / Allgemein', 'Waschkeller', 'waschkeller', 'bi bi-droplet-half'],
            ['Gebäude / Allgemein', 'Dachboden', 'dachboden', 'bi bi-house-up'],
            ['Gebäude / Allgemein', 'Möbliert', 'moebliert', 'bi bi-lamp'],
            ['Gebäude / Allgemein', 'Teilmöbliert', 'teilmoebliert', 'bi bi-lamp-fill'],

            // Parken
            ['Parken', 'Stellplatz', 'stellplatz', 'bi bi-p-square'],
            ['Parken', 'Außenstellplatz', 'aussenstellplatz', 'bi bi-p-square'],
            ['Parken', 'Carport', 'carport', 'bi bi-car-front'],
            ['Parken', 'Garage', 'garage', 'bi bi-car-front-fill'],
            ['Parken', 'Tiefgarage', 'tiefgarage', 'bi bi-p-circle'],
            ['Parken', 'E-Ladestation', 'e-ladestation', 'bi bi-ev-station'],
            ['Parken', 'Fahrradstellplatz', 'fahrradstellplatz', 'bi bi-bicycle'],

            // Energie / Nachhaltigkeit
            ['Energie / Nachhaltigkeit', 'Solarthermie', 'solarthermie', 'bi bi-sun'],
            ['Energie / Nachhaltigkeit', 'Photovoltaik', 'photovoltaik', 'bi bi-sun-fill'],
            ['Energie / Nachhaltigkeit', 'Energieeffizient', 'energieeffizient', 'bi bi-lightning-charge'],
            ['Energie / Nachhaltigkeit', 'Passivhausstandard', 'passivhausstandard', 'bi bi-house-check'],

            // Luxus / Besonderheiten
            ['Luxus / Besonderheiten', 'Sauna', 'sauna', 'bi bi-thermometer-sun'],
            ['Luxus / Besonderheiten', 'Pool', 'pool', 'bi bi-water'],
            ['Luxus / Besonderheiten', 'Whirlpool', 'whirlpool', 'bi bi-water'],
            ['Luxus / Besonderheiten', 'Einbauschränke', 'einbauschraenke', 'bi bi-archive'],
            ['Luxus / Besonderheiten', 'Hochwertige Ausstattung', 'hochwertige-ausstattung', 'bi bi-stars'],
            ['Luxus / Besonderheiten', 'Loftcharakter', 'loftcharakter', 'bi bi-building'],
            ['Luxus / Besonderheiten', 'Echtholzparkett', 'echtholzparkett', 'bi bi-tree'],
            ['Luxus / Besonderheiten', 'Designerbad', 'designerbad', 'bi bi-gem'],

            // Sonstiges
            ['Sonstiges', 'Haustiere erlaubt', 'haustiere-erlaubt', 'bi bi-heart'],
            ['Sonstiges', 'Nichtraucherwohnung', 'nichtraucherwohnung', 'bi bi-ban'],
            ['Sonstiges', 'WG-geeignet', 'wg-geeignet', 'bi bi-people'],
            ['Sonstiges', 'Kinderfreundlich', 'kinderfreundlich', 'bi bi-emoji-smile'],
        ];

        $insertData = [];

        foreach ($data as $index => $item) {
            $insertData[] = [
                'kategorie'   => $item[0],
                'bezeichnung' => $item[1],
                'slug'        => $item[2],
                'icon'        => $item[3],
                'sortierung'  => $index + 1,
                'aktiv'       => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
                'deleted_at'  => null,
            ];
        }

        $this->db->table('ausstattungsmerkmale')->insertBatch($insertData);
    }
}