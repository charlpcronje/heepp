<?php
use core\extension\database\Model;
use core\extension\ui\view;

class Wiki extends Console {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index() {
        $this->setWSRight('views/wiki/index.phtml');
    }

    public function icons($iconSet = 'HeEPP') {
        $this->setOffcanvas('HeEPP Icon Wiki','');
    }
}
