<?php

namespace App\Util;

class TokenGenerator
{
    public static function generateSixDigitToken(): string
    {
        $min = 100000;
        $max = 999999;

        $token = random_int($min, $max);

        return sprintf('%06d', $token);
    }
}
