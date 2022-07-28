<?php
namespace core\element\base;
/**
 * @author Charl Cronje <charlcp@gmail.com>
 * @date 17 Dec 2015
 * @time 10:22:39 AM
 */

class textString extends \core\Element {
    public $value;
    
    function __construct($value = null) {
        $this->value = $value;
    }
    
    function render() {
        return $this->value;
    }
}
