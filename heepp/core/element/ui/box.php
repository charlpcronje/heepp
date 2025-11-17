<?php
namespace core\element\ui;
use core\extension\ui\view;

class box extends \core\Element {
    public $title;
    public $type = 'default';


    function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    function getProperties() {
        $this->setData('title',$this->title);
        $this->setData('type', $this->type);
        $this->setData('children',$this->child);
    }

    function render() {
        $this->getProperties();
        return (new view('box.pml',__DIR__))->html;
    }
}
