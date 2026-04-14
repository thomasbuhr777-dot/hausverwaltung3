<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObjekteTable extends Migration
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
                'constraint' => 255,
            ],
            'strasse' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'hausnummer' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'plz' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'ort' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'baujahr' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'gesamtflaeche' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'beschreibung' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktiv', 'inaktiv'],
                'default'    => 'aktiv',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('objekte');
    }

    public function down(): void
    {
        $this->forge->dropTable('objekte', true);
    }
}
