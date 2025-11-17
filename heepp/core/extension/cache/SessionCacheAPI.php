<?php
namespace core\extension\cache;
use core\Heepp;

class SessionCacheAPI extends Heepp {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function flush($key) {
        $this->forget('session.cache');
    }

    public function dec($key,$decValue = 1) {
        $value = $this->get($key);
        $value -= $decValue;
        $this->set($key,$value);
        return $value;
    }

    public function inc($key,$incValue = 1) {
        $value = $this->get($key);
        $value += $incValue;
        $this->set($key,$value);
        return $value;
    }

    public function delete($key) {
        return $this->forget('session.cache.'.$key);
    }

    public function set($key,$data) {
        return $this->session('cache.'.$key,$data);
    }

    public function get($key) {
        return $this->session('cache.'.$key);
    }
}
