<?php
namespace core\system;
use core\Heepp;

class api {
    public static $verb = 'get';
    public static $cache = true;
    private static $apiKey;
    private static $apiObj;
    private static $apiInitiated = false;
    private static $verbs = ['get','post','put','patch','delete'];

    public static function __callStatic($method,$param) {
        if (!self::$apiInitiated) {
            self::initAPIClass();
        }
        if (in_array($method,self::$verbs)) {
            if (!self::$apiInitiated) {
                new \Exception('The API Request Verb (Method) must be the first argument');
            }
            self::$apiObj->verb($method);
            if (is_string($param) || count((array)$param) == 1) {
                self::$apiObj->url(current($param));
            }
            return self::$apiObj;
        }
    }

    private static function initAPIClass() {
        $class = data::get('app.api.cms.class');
        self::$apiKey = Heepp::data('app.api.cms.auth.key');
        self::$apiObj = new $class();
        self::$apiInitiated = true;
        self::$apiObj->setHeader('Authorization','Bearer '.self::$apiKey);
    }
}
