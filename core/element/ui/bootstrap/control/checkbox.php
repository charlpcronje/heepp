<?php
namespace core\element\ui\bootstrap\control;
use core\extension\ui\view;

class checkbox extends \core\Element {
    public $name;
    public $class;
    public $style;
    public $id = null;
    public $label;
    public $readonly;
    public $disabled;
    public $value;
    public $required;
    public $checked;
    public $check;
    public $rel;

    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('name',$this->name);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        
        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }
        $this->setData('id',$this->id);
        $this->setData('label',$this->label);
        $this->setData('readonly',$this->readonly);
        $this->setData('disabled',$this->disabled);
        $this->setData('value',$this->value);
        $this->setData('required',$this->required);
        $this->setData('checked',$this->checked);
        $this->setData('check',$this->check);
        $this->setData('rel',$this->rel);
    }

    function render() {
        $this->getProperties();
        return (new view('checkbox.xml',__DIR__))->html;
    }
}
