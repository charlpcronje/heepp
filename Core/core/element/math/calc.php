<?php
namespace core\element\math;
use core\Element;

class calc extends Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        return eval("return $this->child;");
    }   
}
