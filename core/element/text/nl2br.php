<?php
namespace core\element\text;

class nl2br extends \core\Element {
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        return nl2br($this->child,true);
    }   
}
