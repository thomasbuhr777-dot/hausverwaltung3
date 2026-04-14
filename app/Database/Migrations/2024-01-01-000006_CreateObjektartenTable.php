<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObjektartenTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bezeichnung' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'created_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('bezeichnung');
        $this->forge->createTable('objektarten');

        // Stammdaten direkt befüllen
        $this->db->table('objektarten')->insertBatch([
            ['bezeichnung' => 'Mehrfamilienhaus'],
            ['bezeichnung' => 'Einfamilienhaus'],
            ['bezeichnung' => 'Eigentumswohnung'],
            ['bezeichnung' => 'Gewerbeeinheit'],
            ['bezeichnung' => 'Garage'],
            ['bezeichnung' => 'Sonstiges'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('objektarten', true);
    }
}
