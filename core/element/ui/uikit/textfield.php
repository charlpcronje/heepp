<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class textfield extends uikit {
    public $id;
    public $name;
    public $class;
    public $style;
    public $margin = 'uk-margin';
    public $label;
    public $type = 'text';
    public $placeholder;
    public $required;
    public $disabled;
    public $value;
    public $icon;
    public $iconaction;
    public $icontip;
    public $icontipposition;
    public $iconclass;
    public $iconclick;
    public $hidelastpass = 'true';
    public $size = 'normal';

    public function render() {
        if (!isset($this->id)) {
            $this->id = uniqid('tf-',false);
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

        if (isset($this->icontipposition)) {
            $this->icontipposition = 'pos: '.$this->icontipposition;
        }

        if (isset($this->icontip) && !isset($this->icontipposition)) {
            $this->icontipposition = 'pos: top-left';
        }

        return view::mold('textfield.phtml',__DIR__,$this);
    }
}
