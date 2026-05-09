<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEinheitenarten extends Migration
{
    public function up()
    {
        $this->forge->addField([
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
            'created_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'updated_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('bezeichnung');



        $this->forge->createTable('einheitenarten');
    }

    public function down()
    {
        $this->forge->dropTable('einheitenarten');
    }
}
