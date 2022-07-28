<?php
namespace core\element\math;

class devide extends \core\element\element {
    public $round = 2;
    public $numberformat = 2;
    
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
                    $total = $total / $value;
                } else {
                    $total = $value;
                }
            }
        }
        $newNumber = round($total,intval($this->round));
        $newNumber = number_format($newNumber,intval($this->numberformat));
        return $newNumber;
    }
}
