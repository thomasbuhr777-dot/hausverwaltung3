<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAusstattungsmerkmaleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kategorie' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'bezeichnung' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
        $this->forge->addKey('kategorie');
        $this->forge->addUniqueKey('slug');

        $this->forge->createTable('ausstattungsmerkmale');
    }

    public function down()
    {
        $this->forge->dropTable('ausstattungsmerkmale');
    }
}