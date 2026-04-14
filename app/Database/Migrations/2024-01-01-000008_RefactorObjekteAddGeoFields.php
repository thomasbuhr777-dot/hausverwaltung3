<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorObjekteAddGeoFields extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('objekte', [
            'land' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'ort',
            ],
            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'default'    => null,
                'after'      => 'land',
            ],
            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,7',
                'null'       => true,
                'default'    => null,
                'after'      => 'latitude',
            ],
            'place_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'longitude',
                'comment'    => 'Google Places ID',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('objekte', ['land', 'latitude', 'longitude', 'place_id']);
    }
}
