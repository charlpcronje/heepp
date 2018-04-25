<?php
namespace core\element\format;
use core\Element;

class date extends Element {
    public $format = 'Y-m-d';
    public $default;
    
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        if (!empty($this->child) || empty($this->default)) {
            return date($this->format,strtotime($this->child));
        }
        if (!empty($this->default)) {
            return $this->default;
        }
        return 'Invalid Date';
    }
}
