<?php
namespace App\Service;

class MoneyFormatter
{
    public function format(?float $amount): string
    {
        if (is_null($amount)) return '0 ₸';

        // Get the decimal part of the amount
        $decimalPart = $amount - floor($amount);

        // If the decimal part is zero, format without decimals
        if ($decimalPart == 0) {
            return number_format($amount, 0, '', ' ') . ' ₸';
        } else {
            return number_format($amount, 2, ',', ' ') . ' ₸';
        }
    }
}