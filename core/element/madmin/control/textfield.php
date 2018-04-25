<?php
namespace core\element\madmin\control;
use core\extension\ui\view;

class textfield extends \core\Element {
    public $type = 'text';
    public $name;
    public $value;
    public $class;
    public $style;
    public $id;
    public $label;
    public $size;    // lg, sm
    public $placeholder;
    public $help;
    public $readonly;
    public $disabled;
    public $append;
    public $appendicon;
    public $prepend;
    public $prependicon;
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
        $this->setData('id',$this->id);
        $this->setData('label',$this->label);
        $this->setData('size',$this->size);
        $this->setData('placeholder',$this->placeholder);
        $this->setData('help',$this->help);
        $this->setData('readonly',$this->readonly);
        $this->setData('disabled',$this->disabled);
        $this->setData('append',$this->append);
        $this->setData('appendicon',$this->appendicon);
        $this->setData('prepend',$this->prepend);
        $this->setData('prependicon',$this->prependicon);
        $this->setData('required',$this->required);
        $this->setData('children',$this->child);
    }

    function render() {
        $this->getProperties();
        return (new view('textfield.xml',$this))->html;
    }
}
