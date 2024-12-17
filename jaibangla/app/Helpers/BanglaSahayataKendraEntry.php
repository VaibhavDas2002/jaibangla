<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Scheme;

class BanglaSahayataKendraEntry
{
    public static function decrypto($enctxt, $tckt) {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');
        $arr2 = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $today = date("Ymd");
        $string2 = "";
        $string1 = "";
        $mobenc = substr($enctxt, 0, 10);
        $dthash = substr($enctxt, 10, strlen($enctxt));
        for ($i = 0; $i < strlen($mobenc); $i++) {
            $string2 .= chr(ord($mobenc[$i]) - 3);
        }
        for ($i = 0; $i < strlen($string2); $i++) {
            $string1 .= $arr2[array_search($string2[$i], $arr)];
        }
        if (md5($string1 . $tckt . $today) != $dthash) { // date not matched
            return "INVALID";
        }
        return $string1;
    }
}
