<?php
namespace core\element\core;
use core\extension\ui\view;
use core\Element;

class redirectMessage extends Element {
    public $style;
    public $class;
    public $messages;
    public $inputs;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        $this->setData('redirect.message',$this->session('app.redirect.message'));
        $this->setData('redirect.input',$this->session('app.redirect.input'));
        return view::mold('redirectMessage.phtml',__DIR__,$this);
    }
}
