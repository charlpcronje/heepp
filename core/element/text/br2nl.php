<?php
namespace core\element\text;

class br2nl extends \core\Element {
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n",$this->child);
    }   
}
