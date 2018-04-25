<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\element\ui\uikit;

class section extends uikit {
    public $id;
    public $class;
    public $style;
    public $child;
    public $type = 'none';

    public function __construct() {
        parent::__construct();
    }

    public function render() {
        return view::mold('section.phtml',__DIR__,$this);
    }
}
