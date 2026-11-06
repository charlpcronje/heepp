<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\Element;
use core\element\ui\uikit;

class grid extends uikit {
    public $id;
    public $class;
    public $style;
    public $child;

    function __construct() {
        parent::__construct();
    }

    function render() {
        return view::mold('grid.phtml',__DIR__,$this);
    }
}
