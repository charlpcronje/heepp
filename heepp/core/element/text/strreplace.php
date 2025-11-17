<?php
namespace core\element\text;

class strreplace extends \core\Element {
    public $search = '';
    public $replace = '';
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        return str_replace($this->search,$this->replace,$this->child);
    }   
}
