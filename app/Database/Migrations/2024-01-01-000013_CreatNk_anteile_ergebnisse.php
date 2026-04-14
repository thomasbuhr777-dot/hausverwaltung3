<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNkAnteileTabelleUndErgebnisse extends Migration
{
    public function up(): void
    {
        // ------------------------------------------------------------------
        // nk_positionen_anteile
        // Berechneter Anteil je Einheit pro Kostenposition
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'position_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nk_einheit_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'anteil_prozent'      => ['type' => 'DECIMAL', 'constraint' => '8,4', 'comment' => 'z.B. 23.4500 %'],
            'anteil_betrag'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'berechnungsgrundlage'=> ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true,
                                       'comment' => 'z.B. "68.50 m² von 420.00 m² gesamt"'],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['position_id', 'nk_einheit_id']);
        $this->forge->addForeignKey('position_id',   'nebenkostenpositionen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('nk_einheit_id', 'nk_einheiten',         'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nk_positionen_anteile');

        // ------------------------------------------------------------------
        // nk_ergebnisse
        // Gesamtergebnis je Einheit: Nachzahlung oder Guthaben
        // ------------------------------------------------------------------
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nk_einheit_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kosten_gesamt'         => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'vorauszahlungen_gesamt'=> ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'saldo'                 => ['type' => 'DECIMAL', 'constraint' => '10,2',
                                        'comment' => 'Positiv = Nachzahlung, Negativ = Guthaben'],
            'zahlung_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true,
                                        'comment' => 'Verknüpfte Zahlung nach Abrechnung'],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nk_einheit_id');
        $this->forge->addForeignKey('nk_einheit_id', 'nk_einheiten', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nk_ergebnisse');
    }

    public function down(): void
    {
        $this->forge->dropTable('nk_ergebnisse',        true);
        $this->forge->dropTable('nk_positionen_anteile', true);
    }
}
