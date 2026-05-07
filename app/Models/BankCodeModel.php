<?php

namespace App\Models;

use CodeIgniter\Model;

class BankCodeModel extends Model
{
    protected $table            = 'bank_codes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'bank_code',
        'name',
        'short_name',
        'city',
        'bic',
        'is_primary',
    ];
}