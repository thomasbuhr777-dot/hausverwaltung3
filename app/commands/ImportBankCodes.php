<?php

namespace App\Commands;

use App\Models\BankCodeModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use RuntimeException;

class ImportBankCodes extends BaseCommand
{
    protected $group       = 'Banks';
    protected $name        = 'banks:import';
    protected $description = 'Importiert die Bundesbank-BLZ-Datei als Bankleitzahlen/BIC-Tabelle.';

    public function run(array $params)
    {
        $file = $params[0] ?? null;

        if (! $file || ! is_file($file)) {
            CLI::error('Bitte Pfad zur CSV-Datei angeben.');
            CLI::write('Beispiel: php spark banks:import writable/imports/blz.csv');
            return;
        }

        $delimiter = $this->detectDelimiter($file);

        $handle = fopen($file, 'rb');

        if (! $handle) {
            throw new RuntimeException('CSV-Datei konnte nicht geöffnet werden.');
        }

        $header = fgetcsv($handle, 0, $delimiter);

        if (! $header) {
            fclose($handle);
            throw new RuntimeException('CSV-Datei enthält keinen Header.');
        }

        $header = array_map([$this, 'normalizeHeader'], $header);

        $model = new BankCodeModel();
        $db = db_connect();

        $db->transStart();

        $model->truncate();

        $count = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $data = $this->mapRow($header, $row);

            if (empty($data['bank_code']) || empty($data['name'])) {
                continue;
            }

            $model->insert($data);
            $count++;
        }

        fclose($handle);

        $db->transComplete();

        if ($db->transStatus() === false) {
            CLI::error('Import fehlgeschlagen.');
            return;
        }

        CLI::write("Import abgeschlossen: {$count} Banken importiert.", 'green');
    }

    private function detectDelimiter(string $file): string
    {
        $sample = file_get_contents($file, false, null, 0, 4096);

        $delimiters = [
            ';'  => substr_count($sample, ';'),
            ','  => substr_count($sample, ','),
            "\t" => substr_count($sample, "\t"),
        ];

        arsort($delimiters);

        return array_key_first($delimiters);
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = str_replace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $header);
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);

        return trim($header, '_');
    }

  private function mapRow(array $header, array $row): array
{
    $combined = [];

    foreach ($header as $index => $key) {
        $combined[$key] = trim($row[$index] ?? '', " \t\n\r\0\x0B\"");
    }

    return [
        'bank_code' => $this->value($combined, ['bankleitzahl']),
        'name' => $this->value($combined, ['bezeichnung']),
        'short_name' => $this->value($combined, ['kurzbezeichnung']),
        'city' => $this->value($combined, ['ort']),
        'bic' => $this->normalizeBic($this->value($combined, ['bic'])),
        'is_primary' => $this->isPrimary($this->value($combined, ['merkmal'])),
    ];
}

    private function value(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return null;
    }

    private function normalizeBic(?string $bic): ?string
    {
        if (! $bic) {
            return null;
        }

        $bic = strtoupper(trim($bic));

        return preg_match('/^[A-Z0-9]{8}([A-Z0-9]{3})?$/', $bic) ? $bic : null;
    }

    private function isPrimary(?string $value): int
    {
        // Bundesbank: Merkmal "1" = bankleitzahlführender Zahlungsdienstleister
        return trim((string) $value) === '1' ? 1 : 0;
    }
}