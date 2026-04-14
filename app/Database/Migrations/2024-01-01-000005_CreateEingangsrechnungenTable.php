<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEingangsrechnungenTable extends Migration
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
            // Polymorphe Beziehung: entweder Objekt ODER Einheit
            'objekt_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Gesamt-Objekt Zuweisung',
            ],
            'einheit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Spezifische Einheit Zuweisung',
            ],
            'rechnungsnummer' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'lieferant' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'lieferant_steuernummer' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'rechnungsdatum' => [
                'type' => 'DATE',
            ],
            'faellig_datum' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'nettobetrag' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'steuersatz' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '19.00',
            ],
            'steuerbetrag' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'bruttobetrag' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'kategorie' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'instandhaltung',
                    'renovierung',
                    'verwaltung',
                    'versicherung',
                    'nebenkosten',
                    'strom',
                    'wasser',
                    'heizung',
                    'reinigung',
                    'sonstige',
                ],
                'default' => 'sonstige',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['offen', 'bezahlt', 'ueberfaellig', 'storniert'],
                'default'    => 'offen',
            ],
            'beschreibung' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'datei_pfad' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'comment'    => 'Pfad zur hochgeladenen PDF-Rechnung',
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
        $this->forge->addKey('objekt_id');
        $this->forge->addKey('einheit_id');
        // Soft FK: nicht hart erzwingen wegen polymorpher Beziehung
        $this->forge->createTable('eingangsrechnungen');

        // CHECK Constraint: entweder objekt_id oder einheit_id muss gesetzt sein
        $this->db->query("
            ALTER TABLE eingangsrechnungen
            ADD CONSTRAINT chk_zuweisung
            CHECK (objekt_id IS NOT NULL OR einheit_id IS NOT NULL)
        ");
    }

    public function down(): void
    {
        $this->forge->dropTable('eingangsrechnungen', true);
    }
}
