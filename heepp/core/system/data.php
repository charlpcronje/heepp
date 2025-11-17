<?php
namespace core\system;
use core\Heepp;

class data {
    private static $core;

    public static function get($key,$default = null) {
        $core = self::init();
        return $core->getData($key,$default);
    }

    public static function getSession($key,$default = null) {
        if (self::sessionKeyExists($key)) {
            return self::getSession($key);
        }
        return $default;
    }

    public static function set($key,$data,$duration = 0) {
        $core = self::init();
        $core->setData($key,$data,null,$duration);
        return $data;
    }

    public static function setSession($key,$data,$duration = 0) {
        return self::set('session.'.$key,$data,$duration);
    }

    public static function exist($key) {
        $core = self::init();
        return $core->dataKeyExist($key);
    }

    public static function sessionKeyExists($key) {
        return self::exist('session.'.$key);
    }

    public static function delete($key) {
        $core = self::init();
        $core->forget($key);
        return true;
    }

    public static function forget($key) {
        return self::delete($key);
    }

    public static function inc($key,$incAmount = 1) {
        $core = self::init();
        $value = $core->getData($key);
        if (is_numeric($value)) {
            $value += $incAmount;
            $core->setData($key,$value);
            return $value;
        }
        $core->setData($key,$incAmount);
        return $incAmount;
    }

    public static function dec($key,$decAmount = 1) {
        $core = self::init();
        $value = $core->getData($key);
        if (is_numeric($value)) {
            $value -= $decAmount;
            $core->setData($key,$value);
            return $value;
        }
        $value = $decAmount * -1;
        $core->setData($key,$value);
        return $value;
    }

    private static function init() {
        if (!defined('INIT_CORE_DATA')) {
            self::$core = new Heepp();
            define('INIT_CORE_DATA',true);
        }
        return self::$core;
    }
}
