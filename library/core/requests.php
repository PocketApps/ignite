<?php

class ignite_requests {
    private static function handle_input($database, $value, $name, $required, $errorCode) {
        if ($required && empty($value)) {
            throw new Exception("Unable to process request. Please provide a valid value for the '$name' parameter", $errorCode);
        }

        if (empty($value)) {
            return null;
        }

        if ($database === null) {
            return $value;
        }

        return mysqli_real_escape_string($database, $value);
    }

    public static function get($database, $name, $required) {
        include_once 'exceptions.php';
        $code = ignite_exceptions::code("GET_PARAM_MISSING");

        if (isset($_GET[$name])) {
            return self::handle_input($database, $_GET[$name], $name, $required, $code);
        }

        return self::handle_input($database, null, $name, $required, $code);
    }

    public static function post($database, $name, $required) {
        include_once 'exceptions.php';
        $code = ignite_exceptions::code("POST_PARAM_MISSING");

        if (isset($_POST[$name])) {
            return self::handle_input($database, $_POST[$name], $name, $required, $code);
        }

        return self::handle_input($database, null, $name, $required, $code);
    }
}