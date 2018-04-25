<?php
namespace core\element\debug;
use core\Element;

class printr extends Element  {
    public function __construct($element = 'printr') {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        $this->element = 'pre';
        ob_start();
        print_r($this->child);
        $this->add(ob_get_clean());
        return parent::render();
    }
}
