<?php
namespace core\element\core;
use core\extension\ui\view;
use core\Element;

class corei extends Element {
    public $id;
    public $class;
    public $icon;
    public $tipside;
    public $tip;
    public $action;
    public $style;
    public $rel;
    public $unique;    
    
    // Do net set the following propertiesk, they are just some helpers
    public $data_toggle;
    
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        $this->unique = uniqid();
        $this->icon = 'fa fa-'.$this->icon;
        
        // Check if a class was set for this element and add a space before the class
        if (isset($this->class)) {
            $this->class = ' '.$this->class;
        }
        
        // Check if the tooltip as set
        if (isset($this->tip)) {
            if (!isset($this->tipside)) {
                $this->tipside = 'top';
            }
            $this->data_toggle = 'tooltip';
        }
        
        if (!isset($this->id)) {
            $this->id = $this->unique;
        }

        return view::mold('coreIcon.phtml',__DIR__,$this);
    }
}
