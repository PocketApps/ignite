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

    public static function update($table, array $changes, $whereField, $whereValue) {
        $sql = "UPDATE $table SET ";
        foreach ($changes as $field => $value) {
            $sql .= "$field='$value', ";
        }

        return substr($sql, 0, strlen($sql) - 2) . " WHERE $whereField='$whereValue'";
    }

    public static function update_multiple($table, array $changes, array $where, $allTrue) {
        $sql = "UPDATE $table SET ";
        foreach ($changes as $field => $value) {
            $sql .= "$field='$value', ";
        }

        $sql = substr($sql, 0, strlen($sql) - 2) . " WHERE ";
        foreach ($where as $field => $value) {
            $sql .= "$field='$value' ";
            if ($allTrue) {
                $sql .= "AND ";
            } else {
                $sql .= "OR ";
            }
        }

        if ($allTrue) {
            return substr($sql, 0, strlen($sql) - 4);
        } else {
            return substr($sql, 0, strlen($sql) - 3);
        }
    }

    public static function add_primary_key($table, $column) {
        return "ALTER TABLE '$table'  DROP PRIMARY KEY, ADD PRIMARY KEY ('$column')";
    }

    public static function remove_primary_key($table) {
        return "ALTER TABLE '$table'  DROP PRIMARY KEY";
    }
}