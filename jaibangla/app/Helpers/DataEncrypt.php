<?php


namespace App\Helpers;

use App\LotGenerationFunctionMaster;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\SchemeCapacity;
use App\Scheme;


class DataEncrypt{
    

    public static function encryptCode($key, $iv, $base64OfCompressedData)
    {
        $OPENSSL_CIPHER_NAME = "aes-256-cbc"; //Name of OpenSSL Cipher 
        $CIPHER_KEY_LEN = 32; //256 bits
        if (strlen($key) < $CIPHER_KEY_LEN) {
            $key = str_pad("$key", $CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $CIPHER_KEY_LEN) {
            $key = substr($key, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $encodedEncryptedData = base64_encode(openssl_encrypt($base64OfCompressedData, $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData . ":" . $encodedIV;

        return $encryptedPayload;
    }

    public  static  function decryptCode($key, $data)
    {
        $OPENSSL_CIPHER_NAME = "aes-256-cbc"; //Name of OpenSSL Cipher 
        $CIPHER_KEY_LEN = 32; //256 bits
        if (strlen($key) < $CIPHER_KEY_LEN) {
            $key = str_pad("$key", $CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $CIPHER_KEY_LEN) {
            $key = substr($key, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $parts = explode(':', $data); //Separate Encrypted data from iv.
        $decryptedData = openssl_decrypt(base64_decode($parts[0]), $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, base64_decode($parts[1]));
        
        return $decryptedData;
    }
}