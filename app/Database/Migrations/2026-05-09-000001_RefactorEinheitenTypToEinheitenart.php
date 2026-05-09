<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorEinheitenTypToEinheitenart extends Migration
{
    public function up(): void
    {
        // 1. Sicherstellen, dass einheitenarten bereits existiert
        //    (Migration 2026-02-15-000006 sollte vorher gelaufen sein)

        // 2. Standardeinträge in einheitenarten einfügen (falls noch leer)
        $typen = [
            'Wohnung',
            'Gewerbe',
            'Stellplatz',
            'Lager',
            'Sonstige',
            'Büro',
            'Garage',
            'Keller',
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($typen as $bezeichnung) {
            $existing = $this->db->table('einheitenarten')
                ->where('bezeichnung', $bezeichnung)
                ->get()
                ->getFirstRow();

            if (! $existing) {
                $this->db->table('einheitenarten')->insert([
                    'bezeichnung' => $bezeichnung,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // 3. Spalte einheitenart_id hinzufügen (rohes SQL – CI4 Forge setzt FK bei
        //    addColumn nicht zuverlässig, daher alles manuell)
        $this->db->query('ALTER TABLE einheiten ADD COLUMN einheitenart_id INT UNSIGNED NULL AFTER objekt_id');

        // 4. Bestehende typ-Werte migrieren
        $mapping = [
            'wohnung'    => 'Wohnung',
            'gewerbe'    => 'Gewerbe',
            'stellplatz' => 'Stellplatz',
            'lager'      => 'Lager',
            'sonstige'   => 'Sonstige',
            'büro'       => 'Büro',
            'garage'     => 'Garage',
            'keller'     => 'Keller',
        ];

        foreach ($mapping as $enumVal => $bezeichnung) {
            $art = $this->db->table('einheitenarten')
                ->where('bezeichnung', $bezeichnung)
                ->get()
                ->getFirstRow();

            if ($art) {
                $this->db->query(
                    'UPDATE einheiten SET einheitenart_id = ? WHERE typ = ?',
                    [$art->id, $enumVal]
                );
            }
        }

        // 5. Fallback: noch nicht zugeordnete Einheiten auf "Sonstige" setzen
        $sonstige = $this->db->table('einheitenarten')
            ->where('bezeichnung', 'Sonstige')
            ->get()
            ->getFirstRow();
        if ($sonstige) {
            $this->db->query(
                'UPDATE einheiten SET einheitenart_id = ? WHERE einheitenart_id IS NULL',
                [$sonstige->id]
            );
        }

        // 6. FK auf einheitenarten setzen
        $this->db->query('ALTER TABLE einheiten ADD CONSTRAINT fk_einheiten_einheitenart FOREIGN KEY (einheitenart_id) REFERENCES einheitenarten(id) ON DELETE SET NULL ON UPDATE CASCADE');

        // 7. typ-Spalte entfernen
        $this->db->query('ALTER TABLE einheiten DROP COLUMN typ');

        // 8. Verknüpfungstabelle einheiten_tags anlegen
        $this->db->query('
            CREATE TABLE IF NOT EXISTS einheiten_tags (
                id                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
                einheit_id              INT UNSIGNED NOT NULL,
                ausstattungsmerkmal_id  INT UNSIGNED NOT NULL,
                created_at              DATETIME NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_einheit_merkmal (einheit_id, ausstattungsmerkmal_id),
                CONSTRAINT fk_etags_einheit  FOREIGN KEY (einheit_id)             REFERENCES einheiten(id)           ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_etags_merkmal  FOREIGN KEY (ausstattungsmerkmal_id) REFERENCES ausstattungsmerkmale(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        // Tags-Tabelle entfernen
        $this->db->query('DROP TABLE IF EXISTS einheiten_tags');

        // typ-Spalte wieder hinzufügen
        $this->db->query("ALTER TABLE einheiten ADD COLUMN typ ENUM('wohnung','gewerbe','stellplatz','lager','sonstige','büro','garage','keller') NOT NULL DEFAULT 'wohnung' AFTER bezeichnung");

        // Daten zurückmigrieren
        $mapping = [
            'Wohnung'    => 'wohnung',
            'Gewerbe'    => 'gewerbe',
            'Stellplatz' => 'stellplatz',
            'Lager'      => 'lager',
            'Sonstige'   => 'sonstige',
            'Büro'       => 'büro',
            'Garage'     => 'garage',
            'Keller'     => 'keller',
        ];

        $einheiten = $this->db->query('
            SELECT e.id, ea.bezeichnung
            FROM einheiten e
            LEFT JOIN einheitenarten ea ON ea.id = e.einheitenart_id
        ')->getResultArray();

        foreach ($einheiten as $e) {
            $typVal = $mapping[$e['bezeichnung']] ?? 'sonstige';
            $this->db->query('UPDATE einheiten SET typ = ? WHERE id = ?', [$typVal, $e['id']]);
        }

        // FK und Spalte entfernen
        $this->db->query('ALTER TABLE einheiten DROP FOREIGN KEY fk_einheiten_einheitenart');
        $this->db->query('ALTER TABLE einheiten DROP COLUMN einheitenart_id');
    }
}