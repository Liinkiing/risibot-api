<?php

namespace App\Utils;


class Str
{
    public static function contains(string $str, string $needle): bool
    {
        if (strpos($str, $needle) !== false) {
            return true;
        }

        return false;
    }

    public static function extractDoubleQuoted(string $str): ?string
    {
        if (preg_match('/"([^"]+)"/', $str, $matches)) {
            return $matches[1];
        }

        return null;
    }
}