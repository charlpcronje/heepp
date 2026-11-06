<?php
namespace core\extension\cache;
use core\Heepp;

class MemCacheLoader {
    # Singleton
    protected static $_instance;

    # Configuration needed keys and default values
    protected static $_iniKeys = ['stats_api'      => 'Server','slabs_api' => 'Server','items_api' => 'Server',
                                  'get_api'        => 'Server','set_api' => 'Server','delete_api' => 'Server',
                                  'flush_all_api'  => 'Server','connection_timeout' => 1,'max_item_dump' => 100,
                                  'refresh_rate'   => 2,'memory_alert' => 80,'hit_rate_alert' => 90,
                                  'eviction_alert' => 0,'file_path' => 'Temp/',
                                  'servers'        => ['Default' => ['127.0.0.1:11211' => ['hostname' => '127.0.0.1',
                                                                                           'port'     => 11211]]]];

    protected static $_ini = [];

    protected function __construct() {
        if (Heepp::data('app.system.memcache')) {
            self::$_ini = Heepp::data('app.system.memcache');
        } else {
            # Fallback
            self::$_ini = self::$_iniKeys;
        }
    }

    public static function singleton() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($key) {
        if (isset(self::$_ini[$key])) {
            return self::$_ini[$key];
        }

        return false;
    }

    public function cluster($cluster) {
        if (isset(self::$_ini['servers'][$cluster])) {
            return self::$_ini['servers'][$cluster];
        }
        return [];
    }

    public function server($server) {
        foreach(self::$_ini['servers'] as $cluster => $servers) {
            if (isset(self::$_ini['servers'][$cluster][$server])) {
                return self::$_ini['servers'][$cluster][$server];
            }
        }
        return [];
    }

    public function set($key,$value) {
        self::$_ini[$key] = $value;
    }

    public function path() {
        return self::$_iniPath;
    }

    public function check() {
        # Checking configuration keys
        foreach(array_keys(self::$_iniKeys) as $iniKey) {
            # Ini file key not set
            if (isset(self::$_ini[$iniKey]) === false) {
                return false;
            }
        }
        return true;
    }

    public function write() {
        if ($this->check()) {
            return is_numeric(file_put_contents(self::$_iniPath,'<?php'.PHP_EOL.'return '.var_export(self::$_ini,true).';'));
        }
        return false;
    }
}
