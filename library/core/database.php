<?php

class ignite_database {
    public static function connect($host, $user, $password, $dbname) {
        set_error_handler(function() {});
        $connection = new mysqli($host, $user, $password, $dbname);

        if ($connection->connect_error) {
            include_once 'exceptions.php';
            restore_error_handler();
            throw new Exception('Connection error: ' . $connection->connect_error, ignite_exceptions::code("DATABASE_CONNECTION_ERROR"));
        }

        restore_error_handler();
        return $connection;
    }

    public static function select($table, $field, $value, $limit = -1) {
        $sql = "SELECT * FROM $table WHERE $field='$value'";
        if ($limit === -1) {
            return $sql;
        }

        return $sql . " LIMIT $limit";
    }

    public static function select_multiple($table, array $items, $allTrue, $limit = -1) {
        $sql = "SELECT * FROM $table WHERE";
        foreach ($items as $field => $value) {
            if ($allTrue) {
                $sql .= " $field='$value' AND";
            } else {
                $sql .= " $field='$value' OR";
            }
        }

        if ($allTrue) {
            $sql = substr($sql, 0, strlen($sql) - 3);
        } else {
            $sql = substr($sql, 0, strlen($sql) - 2);
        }


        if ($limit === -1) {
            return $sql;
        }

        return $sql . "LIMIT $limit";
    }

    public static function insert($table, array $item) {
        $sqlStart = "INSERT INTO $table (";
        $sqlEnd = ") VALUES (";
        foreach ($item as $field => $value) {
            $sqlStart .= "$field, ";
            $sqlEnd .= "'$value', ";
        }

        return substr($sqlStart, 0, strlen($sqlStart) - 2) . substr($sqlEnd, 0, strlen($sqlEnd) - 2) . ")";
    }

    public static function insert_multiple($table, array $items) {
        $fieldsDone = false;
        $sqlStart = "INSERT INTO $table (";
        $sqlEnd = ") VALUES (";
        foreach ($items as $item) {
            foreach ($item as $field => $value) {
                if (!$fieldsDone) {
                    $sqlStart .= "$field, ";
                }
                $sqlEnd .= "'$value', ";
            }
            $fieldsDone = true;
            $sqlEnd = substr($sqlEnd, 0, strlen($sqlEnd) - 2) . "), (";
        }

        return substr($sqlStart, 0, strlen($sqlStart) - 2) . substr($sqlEnd, 0, strlen($sqlEnd) - 3);
    }
}