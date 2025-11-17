<?php
namespace core\element\ui\control;
use core\extension\ui\view;


class multiselect extends \core\Element {
    public $id = null;
    public $options;
    public $select;
    public $name;
    public $label;
    public $class;
    
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function addOptions() {
        $options = "";
        if (isJson($this->select)) {
            $this->select = json_decode($this->select,true);
        }
        if (strpos($this->options,'{') !== false) {
            foreach (json_decode($this->options,true) as $key => $value) {
                if (is_array($this->select)) {
                    if (in_array($key,$this->select)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                } else {
                    if ($key = $this->select) {
                        $select = 'selected';
                    } else {
                        $select = false;
                    }
                }
                $options .= '<option value="'.$key.'" '.$select.'>'.$value.'</option>';
            }
        } else {
            foreach(explode(',',$this->options) as $option) {
                if (is_array($this->select)) {
                    if (in_array($key,$this->select)) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                } else {
                    if ($key = $this->select) {
                        $select = 'selected';
                    } else {
                        $select = false;
                    }
                }
                $options .= '<option value="'.$option.'" '.$select.'>'.$option.'</option>';
            }
        }
        $this->child = $options.$this->child;
    }

    function render() {        
        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }
        
        if (!empty($this->options)) {
            $this->addOptions();
        }

        $this->setData('children',$this->child);
        foreach(classProperties($this) as $key => $value) {
            $this->setData($key, $value);
        }
        return (new view('multiselect.phtml',__DIR__))->html;
    }
}
