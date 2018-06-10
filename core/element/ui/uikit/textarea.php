<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class textarea extends uikit {
    public $id;
    public $name;
    public $class;
    public $style;
    public $label;
    public $rows = 3;
    public $placeholder;
    public $required;
    public $disabled;
    public $value;
    public $size = 'normal';

    public function render() {
        if (!isset($this->id)) {
            $this->id = uniqid('ta-',false);
        }
        switch($this->size) {
            case 'large':
                $this->size = ' uk-form-large';
                break;
            case 'small':
                $this->size = ' uk-form-small';
                break;
            default :
                unset($this->size);
                break;
        }

        return view::mold('textarea.phtml',__DIR__,$this);
    }
}
