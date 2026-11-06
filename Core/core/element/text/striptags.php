<?php
namespace core\element\text;
use core\Element;

class striptags extends Element {
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        return strip_tags(trim($this->child));
    }
}
