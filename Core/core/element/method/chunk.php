<?php
namespace core\element\method;
use core\extension\ui\view;
use core\Element;

class chunk extends Element {
    public $data;
    public $size = 3;
    public $as = 'chunk';

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        $chunks = [];
        if (isset($this->data)) {
            if (!is_array($this->data)) {
                $this->data = json_decode($this->data);
            }
            $chunks = array_chunk($this->data,$this->size);
        }
        $this->setData($this->as,$chunks);
    }
}
