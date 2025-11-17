<?php
namespace core\element\app;
use core\extension\ui\view;
use core\Element;

class wsTopTab extends Element {
    public $id;
    public $iconType = 'UIKit';
    public $icon     = 'file';
    public $title    = 'Untitled';
    public $class;
    public $style;
    public $active = 'inactive';
    public $target = '#cc-workspace .ws-left';

    public function render() {
        if (strpos('fa ',$this->icon) !== false) {
            $this->iconType = 'fontAwesome';
        }
        if ($this->active != 'inactive' && $this->active != 'false') {
            $this->active = 'active';
        }
        return view::mold('wsTopTab.phtml',__DIR__,$this);
    }
}
