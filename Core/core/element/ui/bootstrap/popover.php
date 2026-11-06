<?php
namespace core\element\ui;
use core\extension\ui\view;

class popover extends \core\Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    function render() {
        return (new view('popover.pml',__DIR__))->html;
    }
}
