<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\Element;
use core\element\ui\uikit;

class card extends uikit {
    public $id;
    public $class;
    public $style;
    public $child;
    public $heading;
    public $subheading;

    public function render() {
        return view::mold('card.phtml',__DIR__,$this);
    }
}
