<?php
namespace core\element\math;

class multiply extends \core\element\element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        $values = explode(' ',$this->child);
        $total = null;
        foreach($values as $value) {
            if (is_numeric($value)) {
                if (isset($total)) {
                    $total = $total * $value;
                } else {
                    $total = $value;
                }
            }
        }
        return $total;
    }
}
