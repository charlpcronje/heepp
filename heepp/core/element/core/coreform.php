<?php
namespace core\element\core;
use core\extension\ui\view;
use core\Element;

class coreform extends Element {
    public $id = null;
    public $method;
    public $action;
    public $style;
    public $class;
    public $unique;
    
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        $this->unique = uniqid();
        return view::mold('coreForm.phtml',__DIR__,$this);
    }
}
