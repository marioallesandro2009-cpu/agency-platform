<?php

namespace App\Support;

class PriceFormatter
{
    public static function idr(int|float $price): string
    {
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
