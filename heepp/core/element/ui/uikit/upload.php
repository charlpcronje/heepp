<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class upload extends uikit {
    public $id;
    public $class;
    public $style;
    public $child;
    public $url = 'Upload';
    public $name;
    public $folder;
    public $value;
    public $type = 'image';

    public function render() {
        return view::mold('upload.phtml',__DIR__,$this);
    }
}
