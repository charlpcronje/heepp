<?php
namespace core\element\method;
use core\Element;
use core\extension\ui\view;

class loop extends Element {
    public $from;
    public $to;
    public $as = 'value';
    public $loops = [];
    
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        $i = 0;
        foreach (range($this->from, $this->to) as $curLoop) {
            $this->loops[] = $curLoop;
        }
        $html = '';
        foreach($this->loops as $loop) {
            $html .= str_replace('{'.$this->as.'}',$loop,trim($this->child));
        }
        return $html;
    }
}
