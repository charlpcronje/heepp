<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class dropdown extends uikit {
    public $id;
    public $name;
    public $class;
    public $style;
    public $select;
    public $label;
    public $placeholder;
    public $required;
    public $disabled;
    public $child;

    public function render() {
        if (!isset($this->id)) {
            $this->id = uniqid('tf-',false);
        }
        return view::mold('dropdown.phtml',__DIR__,$this);
    }
}
