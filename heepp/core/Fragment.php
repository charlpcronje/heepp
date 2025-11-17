<?php
namespace core;
class Fragment extends Element {
    protected $fragment;
    protected $child;
    protected $html;
    protected $attr = [];
    
    public function __construct($fragment = null) {
        if (isset($fragment)) {
            $this->fragment = $fragment;
        }
        parent::__construct();
    }
    
    public function render() {
    
    }
}
