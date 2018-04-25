<?php
namespace core\element\madmin\ui;
use core\extension\ui\view;

class breadcrumbs extends \core\Element {
    public $breadcrumbs;
    
    function __construct($breadbrumbs) {
        $this->element = __class__;
        parent::__construct(__class__);
        
        $this->breadcrumbs = $breadbrumbs;
        $this->render();
    }

    function render() {
        $breadcrumbs = [];
        $i = 0;
        $count = count($this->breadcrumbs);
        foreach($this->breadcrumbs as $key => $value) {
            $i++;
            
            if ($i == $count) {
                $breadcrumbs[] = [
                    'crumb'  => $key,
                    'link'   => $value,
                    'active' => 'active'
                ];
            } else {
                $breadcrumbs[] = [
                    'crumb'  => $key,
                    'link'   => $value
                ];
            }
        }
        
        $this->setData('breadcrumb',$breadcrumbs);
        $this->setHtml('#wbreadcrumbs',(new view('breadcrumbs.xml',__DIR__))->html);
    }
}
