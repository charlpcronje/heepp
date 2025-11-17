<?php
namespace core\element\access;
use core\Element;
use core\extension\database\Model;

class hasAccess extends Element {
    public $usertype;
    public $controller;
    public $method;
    public $hasAccess          = true;
    public $createChildObjects = true;
    
    public function __construct($tag = null) {
        $this->element = __class__;
        parent::__construct($tag);
    }
    
    public function checkAccess() {
        $user = null;
        if (empty($this->session('user.email'))) {
            $this->hasAccess = false;
        }
        if (isset($this->usertype)) {
            if (!empty($this->session('user.id'))) {
                $user = Model::mold('user')->find($this->session('user.id'));
            }
            $usertypes = explode(',',(string)$this->usertype);
            if (is_array($usertypes)) {
                if (!empty($this->session('user.id'))) {
                    if (!in_array($user->user_type,$usertypes)) {
                        $this->hasAccess = false;
                    }
                }
            } else {
                if ($user['user_type'] != (string)$this->usertype) {
                    $this->hasAccess = false;
                }
            }
        }

        if (isset($this->controller) && $this->session('user.id') !== null) {
            $model = new Model('userPermission');
            $model->loadDataSet('byUserAndController')->userId($this->session('user.id'))->controller($this->controller)->runDataSet();
            if (!$model->gotResults()) {
                $this->hasAccess = false;
            }
        }

        if (isset($this->controller) && isset($this->method) && $this->session('user.id') !== null) {
            $model = new Model('userPermission');
            $model->loadDataSet('byUserAndController')->userId($this->session('user.id'))->controller($this->controller)->method($this->method)->runDataSet();
            if (!$model->gotResults) {
                $this->hasAccess = false;
            }
        }
    }
    
    public function render() {
        $this->checkAccess();
        if ($this->hasAccess) {
            return $this->child;
        }
        $this->createChildObjects = false;
        return false;
    }
}
