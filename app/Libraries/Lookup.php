<?php

namespace App\Libraries;

use App\Models\LookupModel;

class Lookup
{
    protected static array $allowedTables = [
        'objektarten',
        'etagen',
        'einheitenarten',
        'ausstattungen',
        'heizungsarten',
        'energieausweis_typen',
    ];

    protected static function model(string $table): LookupModel
    {
        if (! in_array($table, self::$allowedTables, true)) {
            throw new \InvalidArgumentException("Lookup table '{$table}' ist nicht erlaubt.");
        }

        return (new LookupModel())->forTable($table);
    }

    public static function rows(string $table, bool $onlyActive = true): array
    {
        return self::model($table)->getItems($onlyActive ? 'active' : 'all');
    }

    public static function pairs(string $table, bool $onlyActive = true): array
    {
        $rows = self::rows($table, $onlyActive);
        $pairs = [];

        foreach ($rows as $row) {
            $pairs[(int) $row['id']] = $row['bezeichnung'];
        }

        return $pairs;
    }

    public static function objektarten(bool $onlyActive = true): array
    {
        return self::pairs('objektarten', $onlyActive);
    }

    public static function etagen(bool $onlyActive = true): array
    {
        return self::pairs('etagen', $onlyActive);
    }

    public static function einheitenarten(bool $onlyActive = true): array
    {
        return self::pairs('einheitenarten', $onlyActive);
    }

    public static function ausstattungen(bool $onlyActive = true): array
    {
        return self::pairs('ausstattungen', $onlyActive);
    }

    public static function heizungsarten(bool $onlyActive = true): array
    {
        return self::pairs('heizungsarten', $onlyActive);
    }

    public static function energieausweisTypen(bool $onlyActive = true): array
    {
        return self::pairs('energieausweis_typen', $onlyActive);
    }
}