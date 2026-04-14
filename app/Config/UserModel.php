<?php

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected $allowedFields = [
        'username',
        'status',
        'status_message',
        'active',
        'last_active',
        'anrede',
        'vorname',
        'nachname',
        'mobile',
        'avatar',
    ];
}


/*
 'adress_art' => ['type' => 'INT', 'null' => true],
            'anrede' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'titel' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'firmenname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'vorname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nachname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'firma' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'strasse' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'plz' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'ort' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'land' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'telefon1' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'telefon2' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'steuer_id' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'bankverbindung' => ['type' => 'TEXT', 'null' => true],
            'notizen' => ['type' => 'TEXT', 'null' => true],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME NULL',
            'created_by' => ['type' => 'INT', 'null' => true],
            'updated_by' => ['type' => 'INT', 'null' => true],
            */