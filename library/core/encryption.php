<?php

class ignite_encryption {
    private static function generate($seed) {
        try {
            $key = "[ignite]-" . $seed . "-[ignite]";
            $temp = "";

            for ($i = 0; $i < strlen($key); $i++) {
                if ($i !== 0) {
                    $oldChar = substr($key, $i - 1, 1);
                    $char = substr($key, $i, 1);
                    if (strrev(ord($char)) % strrev(ord($oldChar)) === 0) {
                        $temp .= abs(abs(strrev(ord($char))) - abs(ord($oldChar)));
                    } else {
                        $temp .= abs(abs(strrev(ord($char))) + abs(strrev(ord($oldChar))));
                    }
                } else {
                    $char = substr($key, $i, 1);
                    $temp .= strrev(ord($char));
                }
            }

            $tempKey1 = str_replace("=", "", base64_encode(strrev(base64_encode($temp))) . strrev(base64_encode(base64_encode($temp))));
            $tempKey2 = str_replace("=", "", base64_encode(strrev(base64_encode($tempKey1))) . strrev(base64_encode(base64_encode($tempKey1))));

            $finalKey = str_replace("=", "", strrev(base64_encode($tempKey2)));
            return $finalKey;
        } catch (Exception $e) {
            return "";
        }
    }

    private static function obscure($seed) {
        $generatedKey = base64_encode(self::encrypt_aes(substr(self::generate(base64_encode(base64_encode(self::generate($seed)) .
            base64_encode(self::generate($seed)))), 0, 2048), base64_encode($seed)));
        $key = base64_encode(self::encrypt_aes(substr(self::generate(base64_encode(base64_encode(self::generate("ignite")) .
            base64_encode(self::generate("ignite")))), 0, 2048), base64_encode("ignite")));
        $temp = base64_encode(self::encrypt_aes(base64_encode(strrev(base64_encode(strrev(base64_encode($generatedKey))))), $key));
        return substr($temp, 0, strlen($temp) - 1);
    }

    public static function encrypt_aes($value, $key, $obscureKey = false) {
        include_once dirname(__DIR__) . '/encryption/Crypt/AES.php';
        $cipher = new Crypt_AES(CRYPT_AES_MODE_CBC);
        if ($obscureKey) {
            $key = self::obscure($key);
        }
        $cipher->setKey($key);
        return $cipher->encrypt($value);
    }

    public static function decrypt_aes($value, $key, $obscureKey = false) {
        include_once dirname(__DIR__) . '/encryption/Crypt/AES.php';
        $cipher = new Crypt_AES(CRYPT_AES_MODE_CBC);
        if ($obscureKey) {
            $key = self::obscure($key);
        }
        $cipher->setKey($key);
        return $cipher->decrypt($value);
    }

    public static function generate_keys($seed) {
        include_once dirname(__DIR__) . '/encryption/Crypt/RSA.php';
        $internalSeed = strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))) .
            strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))) .
            strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))) .
            strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));

        $master = base64_encode(self::encrypt_aes(substr(self::generate(base64_encode(base64_encode(self::generate($internalSeed)) .
            base64_encode(self::generate($seed)))), 0, 2048), base64_encode($seed)));

        $rsa = new Crypt_RSA();
        $rsa->setPrivateKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);
        $rsa->setComment('ignite-generated-key');
        define('CRYPT_RSA_EXPONENT', 65537);
        define('CRYPT_RSA_SMALLEST_PRIME', 64);
        extract($rsa->createKey(4096));

        return array(
            "private" => $privatekey,
            "public" => $publickey,
            "master" => $master
        );
    }
}