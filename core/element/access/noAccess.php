<?php
namespace core\element\access;
use core\Element;

class noAccess extends Element {
    public $createChildObjects = true;

    public function __construct($tag = null) {
        $this->element = __class__;
        parent::__construct($tag);
    }

    public function render() {
        if (!$this->sessionKeyExist('user.id') && !$this->session($this->sessionKeyExist('user.email'))) {
            return $this->child;
        }
        $this->createChildObjects = false;
        return $this->child;
    }
}
