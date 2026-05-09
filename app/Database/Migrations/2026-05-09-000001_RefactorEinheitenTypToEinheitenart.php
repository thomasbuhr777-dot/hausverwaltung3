<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorEinheitenTypToEinheitenart extends Migration
{
    public function up(): void
    {
        $this->ensureEinheitenartenLookupColumns();
        $this->seedEinheitenarten();

        if (! $this->db->fieldExists('einheitenart_id', 'einheiten')) {
            $this->forge->addColumn('einheiten', [
                'einheitenart_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                    'null'     => true,
                    'after'    => 'objekt_id',
                ],
            ]);
        }

        if ($this->db->fieldExists('typ', 'einheiten')) {
            $this->migrateTypValuesToEinheitenart();
        }

        $this->setFallbackEinheitenart();
        $this->addEinheitenartForeignKey();

        if ($this->db->fieldExists('typ', 'einheiten')) {
            $this->forge->dropColumn('einheiten', 'typ');
        }

        if (! $this->db->tableExists('einheiten_tags')) {
            $this->db->query('
                CREATE TABLE einheiten_tags (
                    id                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    einheit_id              INT UNSIGNED NOT NULL,
                    ausstattungsmerkmal_id  INT UNSIGNED NOT NULL,
                    created_at              DATETIME NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY uq_einheit_merkmal (einheit_id, ausstattungsmerkmal_id),
                    CONSTRAINT fk_etags_einheit  FOREIGN KEY (einheit_id)             REFERENCES einheiten(id)           ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT fk_etags_merkmal  FOREIGN KEY (ausstattungsmerkmal_id) REFERENCES ausstattungsmerkmale(id) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');
        }
    }

    public function down(): void
    {
        if ($this->db->tableExists('einheiten_tags')) {
            $this->forge->dropTable('einheiten_tags', true);
        }

        if (! $this->db->fieldExists('typ', 'einheiten')) {
            $this->db->query("ALTER TABLE einheiten ADD COLUMN typ ENUM('wohnung','gewerbe','stellplatz','lager','sonstige','büro','garage','keller') NOT NULL DEFAULT 'wohnung' AFTER bezeichnung");
        }

        if ($this->db->fieldExists('einheitenart_id', 'einheiten')) {
            $mapping = [
                'Wohnung'    => 'wohnung',
                'Gewerbe'    => 'gewerbe',
                'Stellplatz' => 'stellplatz',
                'Lager'      => 'lager',
                'Sonstige'   => 'sonstige',
                'Büro'       => 'büro',
                'Garage'     => 'garage',
                'Keller'     => 'keller',
            ];

            $einheiten = $this->db->query('
                SELECT e.id, ea.bezeichnung AS einheitenart_bezeichnung
                FROM einheiten e
                LEFT JOIN einheitenarten ea ON ea.id = e.einheitenart_id
            ')->getResultArray();

            foreach ($einheiten as $einheit) {
                $typ = $mapping[$einheit['einheitenart_bezeichnung'] ?? ''] ?? 'sonstige';
                $this->db->query('UPDATE einheiten SET typ = ? WHERE id = ?', [$typ, $einheit['id']]);
            }

            $this->dropForeignKeyIfExists('einheiten', 'fk_einheiten_einheitenart');
            $this->forge->dropColumn('einheiten', 'einheitenart_id');
        }
    }

    private function ensureEinheitenartenLookupColumns(): void
    {
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

    private function seedEinheitenarten(): void
    {
        $typen = [
            'Wohnung',
            'Gewerbe',
            'Stellplatz',
            'Lager',
            'Sonstige',
            'Büro',
            'Garage',
            'Keller',
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($typen as $bezeichnung) {
            $exists = $this->db->table('einheitenarten')
                ->where('bezeichnung', $bezeichnung)
                ->countAllResults() > 0;

            if (! $exists) {
                $this->db->table('einheitenarten')->insert([
                    'bezeichnung' => $bezeichnung,
                    'sortierung'  => $this->nextSortierung(),
                    'aktiv'       => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }

    private function nextSortierung(): int
    {
        $row = $this->db->table('einheitenarten')
            ->selectMax('sortierung', 'max_sortierung')
            ->get()
            ->getRowArray();

        return ((int) ($row['max_sortierung'] ?? 0)) + 1;
    }

    private function migrateTypValuesToEinheitenart(): void
    {
        $mapping = [
            'wohnung'    => 'Wohnung',
            'gewerbe'    => 'Gewerbe',
            'stellplatz' => 'Stellplatz',
            'lager'      => 'Lager',
            'sonstige'   => 'Sonstige',
            'büro'       => 'Büro',
            'garage'     => 'Garage',
            'keller'     => 'Keller',
        ];

        foreach ($mapping as $typ => $bezeichnung) {
            $art = $this->db->table('einheitenarten')
                ->where('bezeichnung', $bezeichnung)
                ->get()
                ->getFirstRow();

            if ($art) {
                $this->db->query(
                    'UPDATE einheiten SET einheitenart_id = ? WHERE typ = ?',
                    [$art->id, $typ]
                );
            }
        }
    }

    private function setFallbackEinheitenart(): void
    {
        $sonstige = $this->db->table('einheitenarten')
            ->where('bezeichnung', 'Sonstige')
            ->get()
            ->getFirstRow();

        if ($sonstige) {
            $this->db->query(
                'UPDATE einheiten SET einheitenart_id = ? WHERE einheitenart_id IS NULL',
                [$sonstige->id]
            );
        }
    }

    private function addEinheitenartForeignKey(): void
    {
        if ($this->foreignKeyExists('einheiten', 'fk_einheiten_einheitenart')) {
            return;
        }

        $this->db->query('ALTER TABLE einheiten ADD CONSTRAINT fk_einheiten_einheitenart FOREIGN KEY (einheitenart_id) REFERENCES einheitenarten(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        if ($this->db->DBDriver !== 'MySQLi') {
            return false;
        }

        $row = $this->db->query(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $constraint]
        )->getRowArray();

        return $row !== null;
    }

    private function dropForeignKeyIfExists(string $table, string $constraint): void
    {
        if ($this->foreignKeyExists($table, $constraint)) {
            $this->db->query(sprintf('ALTER TABLE %s DROP FOREIGN KEY %s', $table, $constraint));
        }
    }
}
