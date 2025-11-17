<?php
namespace core\element\madmin\control;
use core\extension\ui\view;

class txtarea extends \core\Element {
    public $name;
    public $value;
    public $class;
    public $style;
    public $rows;
    public $id;
    public $label;
    public $placeholder;
    public $help;
    public $readonly;
    public $disabled;
    public $required;

    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('type',$this->type);
        $this->setData('name',$this->name);
        $this->setData('value',$this->value);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        $this->setData('rows',$this->rows);
        $this->setData('id',$this->id);
        $this->setData('label',$this->label);
        $this->setData('placeholder',$this->placeholder);
        $this->setData('help',$this->help);
        $this->setData('readonly',$this->readonly);
        $this->setData('disabled',$this->disabled);
        $this->setData('required',$this->required);
        $this->setData('children',$this->child);
    }

    function render() {
        $this->getProperties();
        $fo = (new view('txtarea.xml',__DIR__))->html;
    }
}
