<?php
namespace core\extension;
use core\Heepp;

class Extension extends Heepp {
    public function __construct($extension = __CLASS__) {
        parent::__construct($extension);
    }
    
    public function __set($name,$value) {
        parent::__set($name, $value);
    }
}
