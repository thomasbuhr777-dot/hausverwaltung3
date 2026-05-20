<?php

namespace App\Models;

use CodeIgniter\Model;

class BankCodeModel extends Model
{
    protected $table            = 'bank_codes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'erstellt_am';
    protected $updatedField     = 'updated_am';

    protected $allowedFields = [
        'bank_code',
        'name',
        'short_name',
        'city',
        'bic',
        'is_primary',
        'erstellt_von',
        'updated_von',
    ];
}
