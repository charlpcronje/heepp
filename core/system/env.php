<?php
namespace core\system;

class env {
    public static function __callStatic($key,$default = null) {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        if (isset($default)) {
            return $default;
        }
        return null;
    }

    public function add($key,$value) {

    }

    public static function exists($key) {
        return isset($_ENV[$key]) || isset($_SERVER[$key]);
    }
}
