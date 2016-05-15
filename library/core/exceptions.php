<?php

class ignite_exceptions {
    public static function code($key) {
        $codes = array(
            "GET_PARAM_MISSING" => 1,
            "POST_PARAM_MISSING" => 2,
            "DATABASE_CONNECTION_ERROR" => 3,
            "DATABASE_NO_CONNECTION" => 4,
            "DATABASE_ITEM_NOT_FOUND" => 5,
            "DATABASE_ITEM_NOT_UNIQUE" => 6,
            "ENCRYPTION_FAILED" => 7,
            "DECRYPTION_FAILED" => 8,
            "ENCRYPTION_KEYS_GENERATE_FAILED" => 9,
            "ENCRYPTION_KEYS_NOT_GENERATED" => 10
        );

        if (isset($codes[$key])) {
            return $codes[$key];
        }

        return null;
    }

    public static function handle(Exception $e) {
        if (empty($e->getCode())) {
            return json_encode(array(
                "error" => true,
                "message" => $e->getMessage()
            ));
        }

        return json_encode(array(
            "error" => true,
            "code" => $e->getCode(),
            "message" => $e->getMessage()
        ));
    }
}