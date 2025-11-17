<?php
namespace core\element\mobile;

use core\Element;
use core\extension\helper\MobileDetect;

class ismobile extends Element {
    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    public function render() {
        if (!$this->sessionKeyExist('isMobile')) {
            $detect = new MobileDetect();
            if ($detect->isMobile()) {
                $this->session('isMobile',true);
            } else {
                $this->session('isMobile',false);
            }
        }
        
        if ($this->session('isMobile')) {
            return $this->child;
        }
    }
}
