<?php
namespace core\extension\cache;

use core\Heepp;

class cache {
    public static $cacheAPI;

    public static function get($key) {
        self::checkAPIInit();
        return self::$cacheAPI->get($key);
    }

    public static function set($key,$data) {
        self::checkAPIInit();
        return self::$cacheAPI->set($key,$data);
    }

    public static function exists($key) {
        self::checkAPIInit();
        return self::$cacheAPI->get($key) != false && self::$cacheAPI->get($key) !== null && !empty(self::$cacheAPI->get($key));
    }

    public static function delete($key) {
        self::checkAPIInit();
        return self::$cacheAPI->delete($key);
    }

    public static function inc($key,$incAmount = 1) {
        self::checkAPIInit();
        return self::$cacheAPI->increment($key,$incAmount);
    }

    public static function dec($key,$decAmount = 1) {
        self::checkAPIInit();
        return self::$cacheAPI->decrement($key,$decAmount);
    }

    public static function flush($delay = 0) {
        self::checkAPIInit();
        return self::$cacheAPI->flush_all($delay);
    }

    private static function checkAPIInit() {
        if (!defined('CACHE_API_INIT')) {
            if (Heepp::data('app.system.cache.driver') == 'memcached') {
                self::$cacheAPI = new MemCacheAPI();
            }
            if (Heepp::data('app.system.cache.driver') == 'session') {
                self::$cacheAPI = new SessionCacheAPI();
            }
            if (Heepp::data('app.system.cache.driver') == 'file') {
                self::$cacheAPI = new FileCacheAPI();
            }
            define('CACHE_API_INIT',true);
        }
    }
}
