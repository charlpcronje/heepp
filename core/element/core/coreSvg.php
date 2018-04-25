<?php
namespace core\element\core;
use core\Element;
use core\extension\ui\view;

class coreSvg extends Element {
    public $class = 'core-svg';
    public $id = 'core-svg';
    public $src;
    public $path;
    public $url;
    public $file;
    public $style;
    public $fill = '#CCC';
    public $color = '#000';
    public $width = 100;
    public $height = 100;
    public $boxwidth = 100;
    public $boxheight = 100;
    public $version = '1.1';

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        return (new view($this->file,$this->path,$this))->html;
    }
}
