<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMietvertraegeTable extends Migration
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
            'einheit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'vertragsnummer' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'mieter_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'mieter_vorname' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'mieter_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'mieter_telefon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'beginn_datum' => [
                'type' => 'DATE',
            ],
            'ende_datum' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'NULL = unbefristet',
            ],
            'kaltmiete' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'nebenkosten' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'kaution' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'zahlungstag' => [
                'type'       => 'TINYINT',
                'default'    => 1,
                'comment'    => 'Tag im Monat fuer Zahlung',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktiv', 'beendet', 'gekuendigt'],
                'default'    => 'aktiv',
            ],
            'notizen' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('einheit_id');
        $this->forge->addForeignKey('einheit_id', 'einheiten', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mietvertraege');
    }

    public function down(): void
    {
        $this->forge->dropTable('mietvertraege', true);
    }
}
