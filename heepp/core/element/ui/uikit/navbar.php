<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class navbar extends uikit {
    public $child;
    public $id;
    public $class;
    public $style;

    public $imagelogo;
    public $imagelogoheight;
    public $imagelogowidth;

    public $textlogo;
    public $transparent = false;
    public $mode        = 'hover';

    public $height      = '80px';
    public $buttons     = [

    ];

    private function parseProperties() {
        if ($this->transparent) {
            $this->class .= ' uk-navbar-transparent';
        }
    }

    function render() {
        $this->parseProperties();
        return view::mold('navbar.phtml',__DIR__,$this);
    }
}
