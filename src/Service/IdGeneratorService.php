<?php

namespace App\Service;

/**
 * The ID generator service.
 */
class IdGeneratorService
{
    public static function generateUniqueId(int $length = 10): string
    {
        $digits = range('0', '9');
        $uppercaseLetters = range('A', 'Z');

        $characters = array_merge($digits, $uppercaseLetters);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[random_int(0, count($characters) - 1)];
        }

        return $randomString;
    }
}
