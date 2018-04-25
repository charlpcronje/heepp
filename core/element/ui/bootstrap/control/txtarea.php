<?php
namespace core\element\ui\bootstrap\control;
use core\Element;
use core\extension\ui\view;

class txtarea extends Element {
    public $name;
    public $value;
    public $class;
    public $style;
    public $rows;
    public $id;
    public $label;
    public $placeholder;
    public $readonly;
    public $disabled;
    public $required;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }
        return view::mold('txtarea.phtml',__DIR__,$this);
    }
}
