<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveEinheitengeschossLookup extends Migration
{
    public function up(): void
    {
        $this->ensureEinheitenartenLookupColumns();

        if ($this->db->fieldExists('einheitengeschoss_id', 'einheiten')) {
            $this->dropForeignKeysForColumn('einheiten', 'einheitengeschoss_id');
            $this->forge->dropColumn('einheiten', 'einheitengeschoss_id');
        }

        if ($this->db->tableExists('einheitengeschoss')) {
            $this->forge->dropTable('einheitengeschoss', true);
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('einheitengeschoss')) {
            $this->forge->addField($this->lookupFields());
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('bezeichnung');
            $this->forge->createTable('einheitengeschoss');
        }

        if (! $this->db->fieldExists('einheitengeschoss_id', 'einheiten')) {
            $this->forge->addColumn('einheiten', [
                'einheitengeschoss_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'einheitenart_id',
                ],
            ]);
        }
    }

    private function ensureEinheitenartenLookupColumns(): void
    {
        if (! $this->db->tableExists('einheitenarten')) {
            return;
        }

        if (! $this->db->fieldExists('sortierung', 'einheitenarten')) {
            $this->forge->addColumn('einheitenarten', [
                'sortierung' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                    'after'      => 'bezeichnung',
                ],
            ]);
        }

        if (! $this->db->fieldExists('aktiv', 'einheitenarten')) {
            $this->forge->addColumn('einheitenarten', [
                'aktiv' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'sortierung',
                ],
            ]);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function lookupFields(): array
    {
        return [
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bezeichnung' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'sortierung' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'aktiv' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ];
    }

    private function dropForeignKeysForColumn(string $table, string $column): void
    {
        if ($this->db->DBDriver !== 'MySQLi') {
            return;
        }

        $keys = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        )->getResultArray();

        foreach ($keys as $key) {
            $this->db->query(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $key['CONSTRAINT_NAME']));
        }
    }
}
