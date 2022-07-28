<?php
namespace core\element\ui\uikit;
use core\extension\ui\view;
use core\Element;
use core\element\ui\uikit;

class container extends uikit {
    public $id;
    public $class;
    public $style;
    public $child;
    public $sticky;
    public $stickto;

    public function __construct() {
        parent::__construct();
    }

    private function generateSticky() {
        $this->sticky = 'sel-target: '.$this->sticky.'; ';

        if (isset($this->stickto) && !empty($this->stickto)) {
            $this->sticky .= "bottom: ".$this->stickto;
        }
    }

    public function render() {
        // Check if this is a sticky container
        if (isset($this->sticky) && !empty($this->sticky)) {
            $this->generateSticky();
        } else {
            $this->class = "uk-container ".$this->class;
        }
        return view::mold('container.phtml',__DIR__,$this);
    }
}
