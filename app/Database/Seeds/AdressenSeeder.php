<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AdressenSeeder – ~200 realistische Testdatensätze (DE/AT/CH)
 * php spark db:seed AdressenSeeder
 */
class AdressenSeeder extends Seeder
{
    // -----------------------------------------------------------------------
    // Rohdaten
    // -----------------------------------------------------------------------

    private array $vornamenM = [
        'Alexander','Andreas','Benjamin','Christian','Daniel','David','Elias',
        'Felix','Florian','Hans','Heinrich','Jan','Jonas','Julian','Klaus',
        'Lars','Lukas','Marcus','Martin','Matthias','Michael','Niklas','Oliver',
        'Patrick','Peter','Philipp','Robert','Sebastian','Simon','Stefan',
        'Thomas','Tim','Tobias','Uwe','Werner',
    ];

    private array $vornamenW = [
        'Andrea','Angela','Anna','Barbara','Christine','Clara','Elisabeth',
        'Emma','Eva','Franziska','Hannah','Jana','Julia','Katharina','Laura',
        'Lea','Lisa','Maria','Martina','Monika','Nicole','petra','Sabine',
        'Sandra','Sarah','Stefanie','Susanne','Ursula','Verena','Yvonne',
    ];

    private array $nachnamen = [
        'Müller','Schmidt','Schneider','Fischer','Weber','Meyer','Wagner',
        'Becker','Schulz','Hoffmann','Schäfer','Koch','Bauer','Richter',
        'Klein','Wolf','Schröder','Neumann','Schwarz','Zimmermann','Braun',
        'Krüger','Hofmann','Hartmann','Lange','Schmitt','Werner','Krause',
        'Meier','Lehmann','Schmid','Schulze','Maier','Köhler','Herrmann',
        'König','Walter','Mayer','Huber','Kaiser','Fuchs','Peters','Lang',
        'Scholz','Möller','Weiß','Jung','Hahn','Schubert','Vogel','Friedrich',
    ];

    private array $firmen = [
        ['name' => 'Immobilien GmbH & Co. KG',      'suffix' => ''],
        ['name' => 'Hausverwaltung',                  'suffix' => 'GmbH'],
        ['name' => 'Immobilien',                      'suffix' => 'AG'],
        ['name' => 'Grundbesitz',                     'suffix' => 'GbR'],
        ['name' => 'Wohnbau',                         'suffix' => 'GmbH'],
        ['name' => 'Projektentwicklung',              'suffix' => 'GmbH'],
        ['name' => 'Immobilienmanagement',            'suffix' => 'GmbH'],
        ['name' => 'Real Estate',                     'suffix' => 'GmbH'],
        ['name' => 'Vermögensverwaltung',             'suffix' => 'KG'],
        ['name' => 'Facility Management',             'suffix' => 'GmbH'],
        ['name' => 'Bauprojekte',                     'suffix' => 'AG'],
        ['name' => 'Wohnungsbaugesellschaft',         'suffix' => 'mbH'],
        ['name' => 'Property Management',             'suffix' => 'GmbH'],
        ['name' => 'Liegenschaftsverwaltung',         'suffix' => 'GbR'],
        ['name' => 'Stadtentwicklung',                'suffix' => 'GmbH'],
        ['name' => 'Wohnpark',                        'suffix' => 'Verwaltungs-GmbH'],
        ['name' => 'Invest',                          'suffix' => 'AG'],
        ['name' => 'Kapitalanlage',                   'suffix' => 'GmbH'],
        ['name' => 'Eigentümergemeinschaft',          'suffix' => 'GbR'],
        ['name' => 'Grundstücksverwaltung',           'suffix' => 'GmbH & Co. KG'],
    ];

    private array $staedte = [
        ['plz' => '10115', 'ort' => 'Berlin',        'land' => 'Deutschland'],
        ['plz' => '10179', 'ort' => 'Berlin',        'land' => 'Deutschland'],
        ['plz' => '20095', 'ort' => 'Hamburg',       'land' => 'Deutschland'],
        ['plz' => '20099', 'ort' => 'Hamburg',       'land' => 'Deutschland'],
        ['plz' => '80331', 'ort' => 'München',       'land' => 'Deutschland'],
        ['plz' => '80469', 'ort' => 'München',       'land' => 'Deutschland'],
        ['plz' => '50667', 'ort' => 'Köln',          'land' => 'Deutschland'],
        ['plz' => '50823', 'ort' => 'Köln',          'land' => 'Deutschland'],
        ['plz' => '60306', 'ort' => 'Frankfurt am Main', 'land' => 'Deutschland'],
        ['plz' => '60594', 'ort' => 'Frankfurt am Main', 'land' => 'Deutschland'],
        ['plz' => '70173', 'ort' => 'Stuttgart',     'land' => 'Deutschland'],
        ['plz' => '70376', 'ort' => 'Stuttgart',     'land' => 'Deutschland'],
        ['plz' => '40210', 'ort' => 'Düsseldorf',   'land' => 'Deutschland'],
        ['plz' => '40477', 'ort' => 'Düsseldorf',   'land' => 'Deutschland'],
        ['plz' => '04103', 'ort' => 'Leipzig',       'land' => 'Deutschland'],
        ['plz' => '04229', 'ort' => 'Leipzig',       'land' => 'Deutschland'],
        ['plz' => '30159', 'ort' => 'Hannover',      'land' => 'Deutschland'],
        ['plz' => '30175', 'ort' => 'Hannover',      'land' => 'Deutschland'],
        ['plz' => '28195', 'ort' => 'Bremen',        'land' => 'Deutschland'],
        ['plz' => '28209', 'ort' => 'Bremen',        'land' => 'Deutschland'],
        ['plz' => '90402', 'ort' => 'Nürnberg',     'land' => 'Deutschland'],
        ['plz' => '90471', 'ort' => 'Nürnberg',     'land' => 'Deutschland'],
        ['plz' => '01067', 'ort' => 'Dresden',       'land' => 'Deutschland'],
        ['plz' => '01309', 'ort' => 'Dresden',       'land' => 'Deutschland'],
        ['plz' => '44135', 'ort' => 'Dortmund',     'land' => 'Deutschland'],
        ['plz' => '44229', 'ort' => 'Dortmund',     'land' => 'Deutschland'],
        ['plz' => '45127', 'ort' => 'Essen',         'land' => 'Deutschland'],
        ['plz' => '48143', 'ort' => 'Münster',       'land' => 'Deutschland'],
        ['plz' => '76131', 'ort' => 'Karlsruhe',    'land' => 'Deutschland'],
        ['plz' => '79098', 'ort' => 'Freiburg',     'land' => 'Deutschland'],
        ['plz' => '86150', 'ort' => 'Augsburg',     'land' => 'Deutschland'],
        ['plz' => '99084', 'ort' => 'Erfurt',        'land' => 'Deutschland'],
        ['plz' => '1010',  'ort' => 'Wien',          'land' => 'Österreich'],
        ['plz' => '1090',  'ort' => 'Wien',          'land' => 'Österreich'],
        ['plz' => '8010',  'ort' => 'Graz',          'land' => 'Österreich'],
        ['plz' => '5020',  'ort' => 'Salzburg',      'land' => 'Österreich'],
        ['plz' => '6020',  'ort' => 'Innsbruck',     'land' => 'Österreich'],
        ['plz' => '8001',  'ort' => 'Zürich',        'land' => 'Schweiz'],
        ['plz' => '3011',  'ort' => 'Bern',          'land' => 'Schweiz'],
        ['plz' => '4001',  'ort' => 'Basel',         'land' => 'Schweiz'],
    ];

    private array $strassen = [
        'Hauptstraße','Bahnhofstraße','Gartenstraße','Kirchstraße','Schulstraße',
        'Lindenstraße','Bergstraße','Dorfstraße','Friedhofstraße','Waldstraße',
        'Mozartstraße','Goethestraße','Schillerstraße','Beethovenstraße','Bismarckstraße',
        'Kaiserstraße','Wilhelmstraße','Friedrichstraße','Rosenstraße','Parkstraße',
        'Am Markt','An der Kirche','Im Winkel','Auf dem Berg','Am Stadtpark',
        'Ringstraße','Allee der Einheit','Neue Straße','Alte Straße','Marktplatz',
        'Industriestraße','Gewerbepark','Büropark','Technologiestraße','Hansastraße',
        'Mühlenweg','Birkenweg','Eichenweg','Tannenweg','Ahornweg',
        'Breite Straße','Lange Straße','Kurze Gasse','Hintergasse','Vordergasse',
    ];

    private array $anreden  = ['Herr', 'Frau', 'Herr', 'Frau', 'Herr']; // gewichtet
    private array $titel    = ['', '', '', '', '', '', 'Dr.', 'Dr.', 'Prof. Dr.', 'Dipl.-Ing.'];
    private array $banken   = [
        'Deutsche Bank', 'Commerzbank', 'Sparkasse', 'Volksbank', 'DKB',
        'ING', 'Postbank', 'Hypovereinsbank', 'Santander', 'Targobank',
        'Raiffeisenbank', 'Stadtsparkasse', 'Kreissparkasse', 'N26', 'Comdirect',
    ];

    // -----------------------------------------------------------------------
    // Hilfsfunktionen
    // -----------------------------------------------------------------------

    private function rnd(array $arr): mixed
    {
        return $arr[array_rand($arr)];
    }

    private function iban(): string
    {
        $bban = str_pad((string) mt_rand(10000000, 99999999), 8, '0') .
                str_pad((string) mt_rand(1000000000, 9999999999), 10, '0');
        return 'DE' . str_pad((string) mt_rand(10, 99), 2) . $bban;
    }

    private function email(string $vorname, string $nachname, string $firma = ''): string
    {
        $domains = ['gmail.com','web.de','gmx.de','t-online.de','outlook.de','yahoo.de','freenet.de'];
        $v = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $vorname));
        $n = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $nachname));
        $f = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $firma));

        $patterns = $firma
            ? ["{$f}@{$this->rnd($domains)}", "info@{$f}.de", "kontakt@{$f}.de"]
            : ["{$v}.{$n}@{$this->rnd($domains)}", "{$v}{$n}@{$this->rnd($domains)}", "{$n}.{$v}@{$this->rnd($domains)}"];

        return str_replace([' ', 'ä','ö','ü','ß'], ['-','ae','oe','ue','ss'], $this->rnd($patterns));
    }

    private function telefon(): string
    {
        $vorwahlen = ['030','040','089','0221','069','0711','0211','0341','0511','0421','0911'];
        return $this->rnd($vorwahlen) . ' ' . mt_rand(100000, 9999999);
    }

    private function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    // -----------------------------------------------------------------------
    // Run
    // -----------------------------------------------------------------------

    public function run(): void
    {
        $records = [];
        $now     = $this->now();

        // ---- 130 Personen ---------------------------------------------------
        for ($i = 0; $i < 130; $i++) {
            $anrede  = $this->rnd($this->anreden);
            $vorname = $anrede === 'Frau'
                ? ucfirst($this->rnd($this->vornamenW))
                : ucfirst($this->rnd($this->vornamenM));
            $nachname = $this->rnd($this->nachnamen);
            $titel    = $this->rnd($this->titel);
            $stadt    = $this->rnd($this->staedte);
            $mitBank  = mt_rand(0, 2) > 0; // 2/3 haben Bankdaten

            $records[] = [
                'kontakt_typ'     => 'person',
                'anrede'          => $anrede,
                'titel'           => $titel,
                'vorname'         => $vorname,
                'nachname'        => $nachname,
                'firmenname'      => null,
                'umsatzsteuer_id' => null,
                'strasse'         => $this->rnd($this->strassen),
                'hsnr'            => (string) mt_rand(1, 120),
                'plz'             => $stadt['plz'],
                'ort'             => $stadt['ort'],
                'land'            => $stadt['land'],
                'lat'             => null,
                'lon'             => null,
                'telefon1'        => $this->telefon(),
                'telefon2'        => mt_rand(0, 3) === 0 ? $this->telefon() : null,
                'email'           => mt_rand(0, 4) > 0 ? $this->email($vorname, $nachname) : null,
                'iban'            => $mitBank ? $this->iban() : null,
                'bank'            => $mitBank ? $this->rnd($this->banken) : null,
                'bemerkungen'     => null,
                'erstellt_am'     => $now,
                'updated_am'      => $now,
                'geloescht_am'    => null,
                'erstellt_von'    => null,
                'updated_von'     => null,
            ];
        }

        // ---- 70 Firmen -------------------------------------------------------
        for ($i = 0; $i < 70; $i++) {
            $namensteil = $this->rnd($this->nachnamen);
            $firmentyp  = $this->rnd($this->firmen);
            $firmenname = $namensteil . ' ' . $firmentyp['name'] .
                          ($firmentyp['suffix'] ? ' ' . $firmentyp['suffix'] : '');
            $stadt      = $this->rnd($this->staedte);
            $slug       = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $namensteil));
            $slug       = str_replace([' ','ä','ö','ü','ß'], ['-','ae','oe','ue','ss'], $slug);

            $records[] = [
                'kontakt_typ'     => 'firma',
                'anrede'          => null,
                'titel'           => null,
                'vorname'         => null,
                'nachname'        => null,
                'firmenname'      => $firmenname,
                'umsatzsteuer_id' => mt_rand(0, 2) > 0
                    ? 'DE' . mt_rand(100000000, 999999999)
                    : null,
                'strasse'         => $this->rnd($this->strassen),
                'hsnr'            => (string) mt_rand(1, 250),
                'plz'             => $stadt['plz'],
                'ort'             => $stadt['ort'],
                'land'            => $stadt['land'],
                'lat'             => null,
                'lon'             => null,
                'telefon1'        => $this->telefon(),
                'telefon2'        => mt_rand(0, 2) === 0 ? $this->telefon() : null,
                'email'           => "info@{$slug}.de",
                'iban'            => $this->iban(),
                'bank'            => $this->rnd($this->banken),
                'bemerkungen'     => null,
                'erstellt_am'     => $now,
                'updated_am'      => $now,
                'geloescht_am'    => null,
                'erstellt_von'    => null,
                'updated_von'     => null,
            ];
        }

        // In 25er-Batches einfügen (DB-Limit)
        foreach (array_chunk($records, 25) as $batch) {
            $this->db->table('adressen')->insertBatch($batch);
        }

        $personen = count(array_filter($records, fn($r) => $r['kontakt_typ'] === 'person'));
        $firmen   = count(array_filter($records, fn($r) => $r['kontakt_typ'] === 'firma'));

        echo "AdressenSeeder abgeschlossen.\n";
        echo "Personen: {$personen} | Firmen: {$firmen} | Gesamt: " . count($records) . "\n";
    }
}
