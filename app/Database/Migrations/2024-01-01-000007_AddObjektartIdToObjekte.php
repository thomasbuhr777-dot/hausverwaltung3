<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddObjektartIdToObjekte extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('objekte', [
            'objektart_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'bezeichnung',
            ],
        ]);

        $this->db->query('
            ALTER TABLE objekte
            ADD CONSTRAINT fk_objekte_objektart
            FOREIGN KEY (objektart_id) REFERENCES objektarten(id)
            ON UPDATE CASCADE ON DELETE SET NULL
        ');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE objekte DROP FOREIGN KEY fk_objekte_objektart');
        $this->forge->dropColumn('objekte', 'objektart_id');
    }
}
