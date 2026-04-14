<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEinheitenTable extends Migration
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
            'objekt_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'bezeichnung' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'typ' => [
                'type'       => 'ENUM',
                'constraint' => ['wohnung', 'gewerbe', 'stellplatz', 'lager', 'sonstige'],
                'default'    => 'wohnung',
            ],
            'etage' => [
                'type'       => 'TINYINT',
                'null'       => true,
            ],
            'flaeche' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'zimmer' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
                'null'       => true,
            ],
            'beschreibung' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['verfuegbar', 'vermietet', 'gesperrt'],
                'default'    => 'verfuegbar',
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
        $this->forge->addKey('objekt_id');
        $this->forge->addForeignKey('objekt_id', 'objekte', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('einheiten');
    }

    public function down(): void
    {
        $this->forge->dropTable('einheiten', true);
    }
}
