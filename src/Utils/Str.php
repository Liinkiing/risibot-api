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

    public static function slugify(string $text): string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function truncate(string $text, int $chars = 25) {
        if (\strlen($text) <= $chars) {
            return $text;
        }
        $text .= ' ';
        $text = substr($text,0,$chars);
        $text = substr($text,0,strrpos($text,' '));
        $text .= '...';
        return $text;
    }
}