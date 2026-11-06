<?php
namespace core\element\system;

class memusage extends \core\Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function convert($size) {
        $unit=array('B','KB','MB','GB','TB','PB');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    function render() {
        return $this->convert(memory_get_usage(TRUE));
    }
}
