<?php
namespace core\extension\cache;
use core\Heepp;

class FileCacheAPI extends Heepp {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function __call($func,$params) {
        return false;
    }

    public function __get($func) {
        return false;
    }

    public function get($key) {
        return false;
    }

    public function delete($key) {
        return false;
    }

    public function increment($key) {
        return false;
    }

    public function exists($key) {
        return false;
    }

    public function decrement($key) {
        return false;
    }
}

