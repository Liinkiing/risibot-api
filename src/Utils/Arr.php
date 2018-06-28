<?php

namespace App\Utils;


class Arr
{
    static public function random (array $arr)
    {
        return $arr[array_rand($arr)];
    }
}