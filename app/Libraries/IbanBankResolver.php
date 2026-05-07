<?php

namespace App\Libraries;

use App\Models\BankCodeModel;
use InvalidArgumentException;

class IbanBankResolver
{
    public function __construct(
        private ?BankCodeModel $bankCodeModel = null
    ) {
        $this->bankCodeModel ??= new BankCodeModel();
    }

    public function resolve(string $iban): ?array
    {
        $iban = $this->normalizeIban($iban);

        if (! $this->isGermanIban($iban)) {
            throw new InvalidArgumentException('Aktuell werden nur deutsche IBANs unterstützt.');
        }

        if (! $this->isValidIbanChecksum($iban)) {
            throw new InvalidArgumentException('Die IBAN-Prüfziffer ist ungültig.');
        }

        $bankCode = substr($iban, 4, 8);

        $bank = $this->bankCodeModel
            ->where('bank_code', $bankCode)
            ->groupStart()
                ->where('is_primary', 1)
                ->orWhere('is_primary', 0)
            ->groupEnd()
            ->orderBy('is_primary', 'DESC')
            ->orderBy('name', 'ASC')
            ->first();

        if (! $bank) {
            return null;
        }

        return [
            'iban'       => $iban,
            'bank_code'  => $bankCode,
            'bank_name'  => $bank['name'],
            'short_name' => $bank['short_name'],
            'city'       => $bank['city'],
            'bic'        => $bank['bic'],
        ];
    }

    public function normalizeIban(string $iban): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($iban)));
    }

    public function isGermanIban(string $iban): bool
    {
        return preg_match('/^DE\d{20}$/', $iban) === 1;
    }

    public function isValidIbanChecksum(string $iban): bool
    {
        $iban = $this->normalizeIban($iban);

        if (! preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }

        $rearranged = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';

        foreach (str_split($rearranged) as $char) {
            if (ctype_alpha($char)) {
                $numeric .= (string) (ord($char) - 55);
            } else {
                $numeric .= $char;
            }
        }

        $remainder = 0;

        foreach (str_split($numeric) as $digit) {
            $remainder = ($remainder * 10 + (int) $digit) % 97;
        }

        return $remainder === 1;
    }
}