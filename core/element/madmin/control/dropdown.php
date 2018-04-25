<?php
namespace core\element\madmin\control;
use core\extension\ui\view;

class dropdown extends \core\Element {
    public $name;
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
    public $select;
    public $options;
    public $required;

    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('name',$this->name);
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
        $this->setData('select',$this->select);
        $this->setData('required',$this->required);
        $this->setData('children',$this->child);
    }

    function addOptions() {
        $options = "";
        foreach(explode(',',$this->options) as $option) {
            $options .= '<option value="'.$option.'">'.$option.'</option>';
        }
        $children = $this->child;
        $this->child = $options.$children;
    }

    function render() {
        if (!empty($this->options)) {
            $this->addOptions();
        }

        $params['dropdown'] = $this->getProperties();
        return (new view('dropdown.xml',__DIR__))->html;
    }
}
