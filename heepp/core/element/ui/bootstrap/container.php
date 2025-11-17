<?php
namespace core\element\ui\bootstrap;
use core\extension\ui\view;

class container extends \core\Element {
    public $id;
    public $class;
    public $style;
    
    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('id',$this->id);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        $this->setData('children',$this->child);
    }

    function render() {
        $this->getProperties();
        return (new view('container.pml',__DIR__))->html;
    }
}
