<?php
namespace core\element\math;
use core\Element;

class sum extends Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        $values = explode(' ',$this->child);
        $total = null;
        foreach($values as $value) {
            //if (is_numeric($value) && !empty($value)) {
                if (isset($total)) {
                    $total = $total + $value;
                } else {
                    $total = $value;
                }
            //}
        }
        return $total;
    }
}
