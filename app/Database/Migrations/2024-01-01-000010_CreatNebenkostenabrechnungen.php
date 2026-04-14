<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNebenkostenabrechnungenTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'objekt_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'bezeichnung'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'jahr'             => ['type' => 'YEAR'],
            'zeitraum_von'     => ['type' => 'DATE'],
            'zeitraum_bis'     => ['type' => 'DATE'],
            'status'           => [
                'type'       => 'ENUM',
                'constraint' => ['entwurf', 'fertig', 'versendet', 'abgeschlossen'],
                'default'    => 'entwurf',
            ],
            'notizen'          => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('objekt_id');
        $this->forge->addForeignKey('objekt_id', 'objekte', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nebenkostenabrechnungen');
    }

    public function down(): void
    {
        $this->forge->dropTable('nebenkostenabrechnungen', true);
    }
}
