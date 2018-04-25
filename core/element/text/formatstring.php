<?php
namespace core\element\text;

class formatstring extends \core\Element  {
    public $type = 'telephone';
    public $format = null;
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        switch($this->type) {
            case 'telephone':
                $code = substr($this->child,0,3);
                $mid = substr($this->child,3,3);
                $end = substr($this->child,6);
                return '('.$code.') '.$mid.'-'.$end;
            break;
            case 'date':
                if (!isset($this->format)) {
                    $this->format = 'Y-m-d';
                }
                return date($this->format,strtotime($this->child));
            break;
        }
    }
}
