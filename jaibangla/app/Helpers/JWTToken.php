<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class JWTToken
{
    public static function getJWTToken($header_array, $payload_array, $secret_key)
    {   
        $header = json_encode($header_array);
        $header = self::base64url_encode($header);
        $payload = json_encode($payload_array);
        $payload = self::base64url_encode($payload);

        $signature = hash_hmac("sha512", $header . "." . $payload, $secret_key, true);
        $signature = self::base64url_encode($signature);

        $token = $header . "." . $payload . "." . $signature;
        return  $token;
    }

    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
