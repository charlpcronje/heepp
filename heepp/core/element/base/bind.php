<?php
namespace core\element\base;
use core\Element;

class bind extends Element {
    public $data;

    public function render() {
        $this->setData($this->data,$this->child);
    }
}
