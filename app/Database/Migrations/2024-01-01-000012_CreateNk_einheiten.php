<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNkEinheitenTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'abrechnung_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'einheit_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mietvertrag_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'mieter_name'           => ['type' => 'VARCHAR', 'constraint' => 255, 'comment' => 'Snapshot zum Zeitpunkt der Abrechnung'],
            'zeitraum_von'          => ['type' => 'DATE'],
            'zeitraum_bis'          => ['type' => 'DATE'],
            'wohnflaeche'           => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'personenanzahl'        => ['type' => 'TINYINT', 'default' => 1],
            'vorauszahlungen_gesamt'=> ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('abrechnung_id');
        $this->forge->addForeignKey('abrechnung_id', 'nebenkostenabrechnungen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('einheit_id', 'einheiten', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nk_einheiten');
    }

    public function down(): void
    {
        $this->forge->dropTable('nk_einheiten', true);
    }
}
