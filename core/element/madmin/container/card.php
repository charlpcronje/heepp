<?php
namespace core\element\madmin\container;
use core\extension\ui\view;

class card extends \core\Element {
    public $cardstyle;
    public $heading;
    // primary-dark, primary, primary-light, primary-bright, accent-dark, accent, accent-light, accent-bright, default-dark,
    // default, default-light, default-bright, gray-dark, gray, gray-light, gray-bright, danger, warning, success, info
    public $headingstyle;
    // xs, sm, regular, lg
    public $headingsize;
    public $outlined = false;
    public $bordered = false;
    public $bodystyle;
    public $class;
    public $style;
    public $id;
    
    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('cardstyle',$this->cardstyle);
        $this->setData('heading',$this->heading);
        $this->setData('headingstyle',$this->headingstyle);
        $this->setData('headingsize',$this->headingsize);
        if ($this->outlined) {
            $this->setData('outlined','card-outlined');
        } else {
            $this->setData('outlined','');
        }
        
        if ($this->bordered) {
            $this->setData('outlined','card-bordered');
        } else {
            $this->setData('outlined','');
        }
        $this->setData('bordered',$this->bordered);
        $this->setData('bodystyle',$this->bodystyle);
        $this->setData('class',$this->class);
        $this->setData('style',$this->style);
        $this->setData('id',$this->id);
        $this->setData('children',$this->child);
    }

    function render() {
        $this->getProperties();
        return (new view('card.xml',__DIR__))->html;
    }
}
