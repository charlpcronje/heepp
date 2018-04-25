<?php
namespace core\element\ui\control;
use core\extension\ui\view;

class summernote extends \core\Element {
    public $id;
    public $value;
    public $name;
    public $label;
    public $style;
    public $height;
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    function render() {
        if (!isset($this->id)) {
            $this->id = uniqid();
        }
        
        $this->setData("uniqueId",$this->id);
        $this->setData('value',$this->child);
        $this->setData('name',$this->name);
        $this->setData('label',$this->label);
        $this->setData('style',$this->style);
        $this->setData('height',$this->height);
        return (new view('summernote.phtml',__DIR__))->html;
    }
}
