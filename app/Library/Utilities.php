<?php

namespace App\Library;

class Utilities {
    public static function uuid()
    {
        $length = 6;
        $x = '';
        for($i = 1; $i <= $length; $i++){
            $x .= random_int(0,255);
        }
        $random = substr($x, 0, $length);
        $year  = date("y", time());
        $day  = date("d", time());
        $hours  = date("H", time());
        $second  = date("s", time());

        $uuid = $random.$year.$day.$hours.$second;
        return $uuid;
    }

    
}