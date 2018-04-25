<?php
namespace core\element\math;
use core\Element;

class round extends Element {
    public $decimals = 2;
    
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        return number_format(round($this->child,$this->decimals),$this->decimals,'.','');
    }
}
