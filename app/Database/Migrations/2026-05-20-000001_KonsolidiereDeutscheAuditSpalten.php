<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KonsolidiereDeutscheAuditSpalten extends Migration
{
    /**
     * Fachliche Tabellen aus dem Dump vom 20.05.2026, die noch alte
     * englische Spalten oder fehlende Audit-Spalten haben.
     * Shield-/Systemtabellen bleiben bewusst unberuehrt.
     *
     * @var array<string, array<string, bool>>
     */
    private array $tables = [
        'ausstattungsmerkmale'      => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
        'bank_codes'                => ['created' => true,  'updated' => true,  'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'eingangsrechnungen'        => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
        'einheiten'                 => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => true,  'updatedBy' => true],
        'einheiten_tags'            => ['created' => true,  'updated' => false, 'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'einheitenarten'            => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => true,  'updatedBy' => true],
        'einheitenlage'             => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => true,  'updatedBy' => true],
        'mietvertraege'             => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
        'nebenkostenabrechnungen'   => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
        'nebenkostenpositionen'     => ['created' => true,  'updated' => true,  'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'nk_einheiten'              => ['created' => true,  'updated' => true,  'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'nk_ergebnisse'             => ['created' => true,  'updated' => true,  'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'nk_positionen_anteile'     => ['created' => true,  'updated' => true,  'deleted' => false, 'createdBy' => false, 'updatedBy' => false],
        'objektarten'               => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => true,  'updatedBy' => true],
        'objekte'                   => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
        'zahlungen'                 => ['created' => true,  'updated' => true,  'deleted' => true,  'createdBy' => false, 'updatedBy' => false],
    ];

    public function up(): void
    {
        foreach ($this->tables as $table => $original) {
            if (! $this->db->tableExists($table)) {
                continue;
            }

            $this->consolidateColumn($table, 'created_at', 'erstellt_am', $this->dateField());
            $this->consolidateColumn($table, 'updated_at', 'updated_am', $this->dateField());
            $this->consolidateColumn($table, 'deleted_at', 'geloescht_am', $this->dateField());
            $this->consolidateColumn($table, 'created_by', 'erstellt_von', $this->userField());
            $this->consolidateColumn($table, 'updated_by', 'updated_von', $this->userField());
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table => $original) {
            if (! $this->db->tableExists($table)) {
                continue;
            }

            $this->rollbackColumn($table, 'erstellt_am', 'created_at', $this->dateField(), $original['created']);
            $this->rollbackColumn($table, 'updated_am', 'updated_at', $this->dateField(), $original['updated']);
            $this->rollbackColumn($table, 'geloescht_am', 'deleted_at', $this->dateField(), $original['deleted']);
            $this->rollbackColumn($table, 'erstellt_von', 'created_by', $this->userField(), $original['createdBy']);
            $this->rollbackColumn($table, 'updated_von', 'updated_by', $this->userField(), $original['updatedBy']);
        }
    }

    private function dateField(): string
    {
        return 'DATETIME NULL DEFAULT NULL';
    }

    private function userField(): string
    {
        return 'INT UNSIGNED NULL DEFAULT NULL';
    }

    private function consolidateColumn(string $table, string $oldName, string $newName, string $definition): void
    {
        $hasOld = $this->hasColumn($table, $oldName);
        $hasNew = $this->hasColumn($table, $newName);

        if ($hasOld && ! $hasNew) {
            $this->db->query(sprintf(
                'ALTER TABLE %s CHANGE %s %s %s',
                $this->identifier($table),
                $this->identifier($oldName),
                $this->identifier($newName),
                $definition
            ));

            return;
        }

        if ($hasOld && $hasNew) {
            $this->db->query(sprintf(
                'UPDATE %s SET %s = COALESCE(%s, %s)',
                $this->identifier($table),
                $this->identifier($newName),
                $this->identifier($newName),
                $this->identifier($oldName)
            ));
            $this->db->query(sprintf(
                'ALTER TABLE %s DROP COLUMN %s',
                $this->identifier($table),
                $this->identifier($oldName)
            ));

            return;
        }

        if (! $hasNew) {
            $this->db->query(sprintf(
                'ALTER TABLE %s ADD COLUMN %s %s',
                $this->identifier($table),
                $this->identifier($newName),
                $definition
            ));
        }
    }

    private function rollbackColumn(
        string $table,
        string $newName,
        string $oldName,
        string $definition,
        bool $existedBefore
    ): void
    {
        $hasNew = $this->hasColumn($table, $newName);
        $hasOld = $this->hasColumn($table, $oldName);

        if (! $existedBefore) {
            if ($hasNew) {
                $this->db->query(sprintf(
                    'ALTER TABLE %s DROP COLUMN %s',
                    $this->identifier($table),
                    $this->identifier($newName)
                ));
            }

            return;
        }

        if ($hasNew && ! $hasOld) {
            $this->db->query(sprintf(
                'ALTER TABLE %s CHANGE %s %s %s',
                $this->identifier($table),
                $this->identifier($newName),
                $this->identifier($oldName),
                $definition
            ));

            return;
        }

        if ($hasNew && $hasOld) {
            $this->db->query(sprintf(
                'UPDATE %s SET %s = COALESCE(%s, %s)',
                $this->identifier($table),
                $this->identifier($oldName),
                $this->identifier($oldName),
                $this->identifier($newName)
            ));
            $this->db->query(sprintf(
                'ALTER TABLE %s DROP COLUMN %s',
                $this->identifier($table),
                $this->identifier($newName)
            ));
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        $row = $this->db->query(
            sprintf('SHOW COLUMNS FROM %s LIKE %s', $this->identifier($table), $this->db->escape($column))
        )->getRowArray();

        return $row !== null;
    }

    private function identifier(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }
}
