<?php
namespace core\element\ui\bootstrap\control;
use core\extension\ui\view;

class dropdown extends \core\Element {
    public $name;
    public $class;
    public $style;
    public $id = null;
    public $label;
    public $size;    // lg, sm
    public $placeholder;
    public $readonly;
    public $disabled;
    public $append;
    public $appendlink;
    public $appendicon;
    public $prepend;
    public $prependicon;
    public $select;
    public $options;
    public $required;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function getProperties() {
        $this->setData('name',$this->name);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        
        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }
        $this->setData('id',$this->id);
        $this->setData('label',$this->label);
        $this->setData('size',$this->size);
        $this->setData('placeholder',$this->placeholder);
        $this->setData('readonly',$this->readonly);
        $this->setData('disabled',$this->disabled);
        $this->setData('append',$this->append);
        $this->setData('appendlink',$this->appendlink);
        $this->setData('appendicon',$this->appendicon);
        $this->setData('prepend',$this->prepend);
        $this->setData('prependicon',$this->prependicon);
        $this->setData('select',$this->select);
        $this->setData('required',$this->required);
        $this->setData('children',$this->child);
        
        if (isset($this->append) || isset($this->appendicon) || isset($this->prepend) || isset($this->prependicon)) {
            $this->setData('group','input-group');
        }
    }

    public function addOptions() {
        $options = '';
        if (strpos($this->options,'{') !== false) {
            foreach (json_decode($this->options,true) as $key => $value) {
                $options .= '<option value="'.$key.'">'.$value.'</option>';
            }
        } else {
            foreach(explode(',',$this->options) as $option) {
                $options .= '<option value="'.$option.'">'.$option.'</option>';
            }
        }
        $this->child = $options.$this->child;
    }

    public function render() {
        if (!empty($this->options)) {
            $this->addOptions();
        }

        $params['dropdown'] = $this->getProperties();
        $fo = new view('dropdown.xml',__DIR__);
        return $fo->html;
    }
}
