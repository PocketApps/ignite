<?php

include_once 'core/requests.php';
include_once 'core/exceptions.php';
include_once 'core/database.php';
include_once 'core/encryption.php';

class ignite {
    private $connected;
    private $database;
    private $privateKey;
    private $publicKey;
    private $masterKey;

    public function get($name, $required = true) {
        return ignite_requests::get(null, $name, $required);
    }

    public function post($name, $required = true) {
        return ignite_requests::post(null, $name, $required);
    }

    public function get_secure($name, $required = true) {
        $this->check_connection();
        return ignite_requests::get($this->database, $name, $required);
    }

    public function post_secure($name, $required = true) {
        $this->check_connection();
        return ignite_requests::post($this->database, $name, $required);
    }

    public function handle_exception(Exception $e) {
        echo ignite_exceptions::handle($e);
    }

    public function connect($host, $user, $password, $dbname) {
        $this->database = ignite_database::connect($host, $user, $password, $dbname);
        $this->connected = $this->database !== null;
    }

    public function select($table, $field, $value, $limit = -1) {
        return ignite_database::select($table, $field, $value, $limit);
    }

    public function query_select($table, $field, $value, $limit = -1) {
        $this->check_connection();
        $sql = ignite_database::select($table, $field, $value, $limit);
        $this->query($sql);
    }

    public function select_multiple($table, array $items, $allTrue, $limit = -1) {
        return ignite_database::select_multiple($table, $items, $allTrue, $limit);
    }

    public function query_select_multiple($table, array $items, $allTrue, $limit = -1) {
        $this->check_connection();
        $sql = ignite_database::select_multiple($table, $items, $allTrue, $limit);
        $this->query($sql);
    }

    public function insert($table, array $item) {
        return ignite_database::insert($table, $item);
    }

    public function query_insert($table, array $item) {
        $this->check_connection();
        $sql = ignite_database::insert($table, $item);
        $this->query($sql);
    }

    public function insert_multiple($table, array $items) {
        return ignite_database::insert_multiple($table, $items);
    }

    public function query_insert_multiple($table, array $items) {
        $this->check_connection();
        $sql = ignite_database::insert_multiple($table, $items);
        $this->query($sql);
    }

    public function update($table, array $changes, $whereField, $whereValue) {
        return ignite_database::update($table, $changes, $whereField, $whereValue);
    }

    public function query_update($table, array $changes, $whereField, $whereValue) {
        $this->check_connection();
        $sql = ignite_database::update($table, $changes, $whereField, $whereValue);
        $this->query($sql);
    }

    public function update_multiple($table, array $changes, array $where, $allTrue) {
        return ignite_database::update_multiple($table, $changes, $where, $allTrue);
    }

    public function query_update_multiple($table, array $changes, array $where, $allTrue) {
        $this->check_connection();
        $sql = ignite_database::update_multiple($table, $changes, $where, $allTrue);
        $this->query($sql);
    }

    public function add_primary_key($table, $column) {
        return ignite_database::add_primary_key($table, $column);
    }

    public function query_add_primary_key($table, $column) {
        $this->check_connection();
        $sql = ignite_database::add_primary_key($table, $column);
        $this->query($sql);
    }

    public function remove_primary_key($table) {
        return ignite_database::remove_primary_key($table);
    }

    public function query_remove_primary_key($table) {
        $this->check_connection();
        $sql = ignite_database::remove_primary_key($table);
        $this->query($sql);
    }

    public function query($sql) {
        $this->check_connection();
        return $this->database->query($sql);
    }

    public function db_contains($table, array $items, $allTrue = true, $message = null) {
        $this->check_connection();
        $count = mysqli_num_rows($this->database->query(ignite_database::select_multiple($table, $items, $allTrue, -1)));
        if ($count <= 0) {
            if (empty($message)) {
                $message = "The required data was not found in the database";
            }
            throw new Exception($message, ignite_exceptions::code("DATABASE_ITEM_NOT_FOUND"));
        }
    }

    public function db_contains_unique($table, array $items, $allTrue = true, $message = null) {
        $this->check_connection();
        $count = mysqli_num_rows($this->database->query(ignite_database::select_multiple($table, $items, $allTrue, -1)));
        if ($count <= 0) {
            if (empty($message)) {
                $message = "The required data was not found in the database";
            }
            throw new Exception($message, ignite_exceptions::code("DATABASE_ITEM_NOT_FOUND"));
        } else if ($count > 1) {
            if (empty($message)) {
                $message = "Multiple items found in database";
            }
            throw new Exception($message, ignite_exceptions::code("DATABASE_ITEM_NOT_UNIQUE"));
        }
    }

    private function check_connection() {
        if (!$this->connected) {
            throw new Exception("Unable to query database. No database connection established",
                ignite_exceptions::code("DATABASE_NO_CONNECTION"));
        }
    }

    public function generate_keys($seed = null) {
        if (empty($seed)) {
            $seed = strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                    mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535))) .
                strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
                    mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
        }
        
        $keys = ignite_encryption::generate_keys($seed);
        $this->privateKey = $keys['private'];
        $this->publicKey = $keys['public'];
        $this->masterKey = $keys['master'];

        if (empty($this->privateKey) || empty($this->publicKey) || empty($this->masterKey)) {
            throw new Exception("Unable to generate encryption keys", ignite_exceptions::code("ENCRYPTION_KEYS_GENERATE_FAILED"));
        }
    }

    public function get_private_key($encode = false) {
        if (empty($this->privateKey) || empty($this->publicKey) || empty($this->masterKey)) {
            throw new Exception("Unable to get keys. No encryption keys were generated",
                ignite_exceptions::code("ENCRYPTION_KEYS_NOT_GENERATED"));
        }
        if ($encode) {
            return base64_encode($this->privateKey);
        }
        return $this->privateKey;
    }

    public function get_public_key($encode = false) {
        if (empty($this->privateKey) || empty($this->publicKey) || empty($this->masterKey)) {
            throw new Exception("Unable to get keys. No encryption keys were generated",
                ignite_exceptions::code("ENCRYPTION_KEYS_NOT_GENERATED"));
        }
        if ($encode) {
            return base64_encode($this->publicKey);
        }
        return $this->publicKey;
    }

    public function get_master_key($encode = false) {
        if (empty($this->privateKey) || empty($this->publicKey) || empty($this->masterKey)) {
            throw new Exception("Unable to get keys. No encryption keys were generated",
                ignite_exceptions::code("ENCRYPTION_KEYS_NOT_GENERATED"));
        }
        if ($encode) {
            return base64_encode($this->masterKey);
        }
        return $this->masterKey;
    }

    public function encrypt_aes($value, $key, $encodeOutput = false, $obscureKey = false) {
        $cipher = ignite_encryption::encrypt_aes($value, $key, $obscureKey);
        if (empty($cipher) && !empty($value)) {
            throw new Exception("Unable to encrypt content", ignite_exceptions::code("ENCRYPTION_FAILED"));
        }
        if ($encodeOutput) {
            return base64_encode($cipher);
        }
        return $cipher;
    }

    public function decrypt_aes($cipher, $key, $decodeInput = false, $obscureKey = false) {
        if ($decodeInput) {
            $cipher = base64_decode($cipher);
        }
        $text = ignite_encryption::decrypt_aes($cipher, $key, $obscureKey);
        if (empty($text) && !empty($cipher)) {
            throw new Exception("Unable to decrypt content", ignite_exceptions::code("DECRYPTION_FAILED"));
        }
        return $text;
    }
}