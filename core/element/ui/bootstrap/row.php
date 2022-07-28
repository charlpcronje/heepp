<?php
namespace core\element\ui\bootstrap;
use core\extension\ui\view;

class row extends \core\Element {
    public $id;
    public $class;
    public $style;
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    function render() {
        $this->setData('id',$this->id);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        $this->setData('children',$this->child);
        
        return (new view('row.pml',__DIR__))->html;
    }
}
