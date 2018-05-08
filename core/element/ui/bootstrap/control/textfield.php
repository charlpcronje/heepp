<?php
namespace core\element\ui\bootstrap\control;
use core\Element;
use core\extension\ui\view;

class textfield extends Element {
    public $type = 'text';
    public $name;
    public $value;
    public $class;
    public $style;
    public $id;
    public $label;
    public $size;    // lg, sm
    public $placeholder;
    public $readonly;
    public $disabled;
    public $append;
    public $appendicon;
    public $appendiconrel;
    public $appendicontooltip;
    public $prepend;
    public $prependicon;
    public $required;
    public $pattern;
    public $mask;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        if (isset($this->mask)) {
            if (strlen($this->class) > 0) {
                $this->class .= ' mask';
            } else {
                $this->class = 'mask';
            }
        }

        if (empty($this->id)) {
            $this->id = 'field'.uniqid();
        }

        if (isset($this->append) || isset($this->appendicon) || isset($this->prepend) || isset($this->prependicon)) {
            $this->group = 'input-group';
        }

        return view::mold('textfield.phtml',__DIR__,$this);
    }
}
