<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankCodesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'bank_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'short_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'bic' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
                'null'       => true,
            ],
            'is_primary' => [
                'type'    => 'TINYINT',
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('bank_code');
        $this->forge->addKey('bic');
        $this->forge->createTable('bank_codes');
    }

    public function down()
    {
        $this->forge->dropTable('bank_codes');
    }
}