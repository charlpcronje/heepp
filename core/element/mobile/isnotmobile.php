<?php
namespace core\element\mobile;

class isnotmobile extends \core\element\element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }
    
    function render() {
        if(preg_match('/(?i)msie [1-11]/',$_SERVER['HTTP_USER_AGENT'])) {
            $_SESSION['isMobile'] = true;
        }
        
        if (!isset($_SESSION['isMobile'])) {
            $detect = new \core\extension\helper\MobileDetect();
            if ($detect->isMobile()) {
                $_SESSION['isMobile'] = true;
            } else {
                $_SESSION['isMobile'] = false;
            }
        }
        
        if (!$_SESSION['isMobile']) {
            return $this->child;
        }
    }
}
