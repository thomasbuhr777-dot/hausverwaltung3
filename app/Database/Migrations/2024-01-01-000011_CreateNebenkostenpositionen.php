<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNebenkostenpositionenTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'abrechnung_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kategorie'             => [
                'type'       => 'ENUM',
                'constraint' => [
                    'heizung', 'warmwasser', 'wasser_abwasser',
                    'muell', 'hausmeister', 'versicherung',
                    'strom_allgemein', 'reinigung', 'aufzug',
                    'gartenpflege', 'verwaltung', 'sonstige',
                ],
                'default' => 'sonstige',
            ],
            'bezeichnung'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'gesamtbetrag'          => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'verteilerschluessel'   => [
                'type'       => 'ENUM',
                'constraint' => ['wohnflaeche', 'personenanzahl', 'verbrauch', 'gleich'],
                'default'    => 'wohnflaeche',
            ],
            'eingangsrechnung_ids'  => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON-Array der zugeordneten Eingangsrechnungs-IDs',
            ],
            'sortierung'            => ['type' => 'TINYINT', 'default' => 0],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('abrechnung_id');
        $this->forge->addForeignKey('abrechnung_id', 'nebenkostenabrechnungen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nebenkostenpositionen');
    }

    public function down(): void
    {
        $this->forge->dropTable('nebenkostenpositionen', true);
    }
}
