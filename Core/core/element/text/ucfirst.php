<?php
namespace core\element\text;
use core\Element;

class ucfirst extends Element {
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        return ucfirst(strtolower($this->child));
    }   
}
