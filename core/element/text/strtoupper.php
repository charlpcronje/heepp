<?php
namespace core\element\text;

class strtoupper extends \core\Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        return strtoupper($this->child);
    }   
}
