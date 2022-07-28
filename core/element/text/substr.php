<?php
namespace core\element\text;
use core\Element;

class substr extends Element {
    public $start = 0;
    public $length = 10;
    public $trim = true;
    
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        if ($this->trim) {
            //if (is_object($this->start)) {
            //    pd($this->start);
            //}
            return substr(trim($this->child),(int)$this->start,(int)$this->length);
        } else {
            return substr($this->child,$this->start,$this->length);
        }
    }   
}
