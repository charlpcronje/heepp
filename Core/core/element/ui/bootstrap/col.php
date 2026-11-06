<?php
namespace core\element\ui\bootstrap;
use core\extension\ui\view;
use core\Element;

class col extends Element {
    public $id;
    public $class;
    public $style;
    
    public $size = 12;
    public $size_xs;
    public $size_sm;
    public $size_md;
    public $size_lg;
    public $children;
    
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        $this->class = ' '.$this->class;
        
        // Large Size
        if (!isset($this->size_lg)) {
            $this->size_lg = $this->size;
        } else {
            $this->size_lg = $this->size_lg;
            $this->size_md = $this->size_lg;
            $this->size_sm = $this->size_lg;
        }
        
        // Medium Size
        if (!isset($this->size_md)) {
            $this->size_md = $this->size;
        } else {
            $this->size_md = $this->size_md;
            $this->size_sm = $this->size_md;
        }
        
        // Small Size
        if (!isset($this->size_sm)) {
            $this->size_sm = $this->size;
        } else {
            $this->size_sm = $this->size_sm;
        }
        
        // Extra Small Size
        if (isset($this->size_xs)) {
            $this->size_xs = $this->size_xs;
        }
        
        if (isset($this->size_xs)) {
            $this->size_xs = 'col-xs-'.$this->size_xs.' ';
        }
        
        $this->size_sm = ' col-sm-'.$this->size_sm;
        $this->size_md = ' col-md-'.$this->size_md;
        $this->size_lg = ' col-lg-'.$this->size_lg;
        $this->children = $this->child;
        
        return (new view('col.pml',__DIR__,$this))->html;
    }
}
