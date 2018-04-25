<?php
namespace core;

class Controller extends Heepp {
    public function __construct($controller = null) {
        if (!isset($controller)) {
            $controller = __CLASS__;
        }
        parent::__construct($controller);
    }
}
