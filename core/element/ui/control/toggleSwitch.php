<?php
namespace core\element\ui\control;
use core\Element;
use core\extension\ui\view;

class toggleSwitch extends Element {
    public $id = null;
    public $checked;
    public $style;
    public $label;
    public $hint;
    public $class;
    public $name;
    public $check;
    public $value;
    // win | win8 | win10 | material
    public $theme = 'win8';

    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    public function render() {
        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }
        if ($this->checked == 1 || $this->checked == true || $this->checked == 'true' || $this->cheched == 'checked') {
            $this->checked = 'checked';
        }
        return (new view('toggleSwitch.phtml',__DIR__,$this))->html;
    }
}
