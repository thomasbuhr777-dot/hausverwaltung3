<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateZahlungenTable extends Migration
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
            'mietvertrag_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'betrag' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'datum' => [
                'type' => 'DATE',
            ],
            'faellig_datum' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'typ' => [
                'type'       => 'ENUM',
                'constraint' => ['miete', 'nebenkosten', 'kaution', 'sonstige'],
                'default'    => 'miete',
            ],
            'zahlungsart' => [
                'type'       => 'ENUM',
                'constraint' => ['ueberweisung', 'lastschrift', 'bar', 'sonstige'],
                'default'    => 'ueberweisung',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['offen', 'bezahlt', 'teilbezahlt', 'ueberfaellig', 'storniert'],
                'default'    => 'offen',
            ],
            'referenz' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Verwendungszweck / Referenz',
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
        $this->forge->addKey('mietvertrag_id');
        $this->forge->addForeignKey('mietvertrag_id', 'mietvertraege', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('zahlungen');
    }

    public function down(): void
    {
        $this->forge->dropTable('zahlungen', true);
    }
}
