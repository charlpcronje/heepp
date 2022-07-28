<?php
namespace core\element\base;
use core\Element;

class textNode extends Element {
    public function render() {
        parent::render();
        $this->setElement('');
        return $this->child;
        //return $this->child;
    }
}
