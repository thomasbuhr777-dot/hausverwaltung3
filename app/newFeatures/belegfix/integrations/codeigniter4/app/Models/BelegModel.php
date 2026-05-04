<?php

namespace App\Models;

use CodeIgniter\Model;

class BelegModel extends Model
{
    protected $table            = 'belege';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'vendor', 
        'invoice_number', 
        'date', 
        'total_amount', 
        'net_amount', 
        'tax_amount', 
        'currency', 
        'iban', 
        'filename'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Validierung der IBAN
     */
    public function validateIban($iban)
    {
        $iban = str_replace(' ', '', $iban);
        return preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban);
    }
}
