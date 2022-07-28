<?php
namespace core\extension\parser\env;
use core\extension\parser\env\exception\InvalidPathException;

/**
 * This is the dotenv class.
 * It's responsible for loading a `.env` file in the given directory and
 * setting the environment vars. */
class DotEnv {
    protected $filePath;
    protected $loader;
    public function __construct($path,$file = '.env',$project = null) {
        $this->filePath = $this->getFilePath($path,$file);
        $this->loader   = new Loader($this->filePath,true,$project);
    }

    //public static function __callStatic($name,$path = null) {
    //    if ($name == 'load' && isset($path)) {
    //        (new DotEnv($path))->load();
    //    }
    //}

    public function load() {
        return $this->loadData();
    }

    public function safeLoad() {
        try {
            return $this->loadData();
        } catch(InvalidPathException $e) {
            // suppressing exception
            return [];
        }
    }

    public function overload() {
        return $this->loadData(true);
    }

    protected function getFilePath($path,$file) {
        if (!is_string($file)) {
            $file = '.env';
        }
        return rtrim($path,DS).DS.$file;
    }

    protected function loadData($overload = false) {
        return $this->loader->setImmutable(!$overload)->load();
    }

    public function required($variable) {
        return new Validator((array)$variable,$this->loader);
    }
}
