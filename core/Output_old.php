<?php
namespace baseCore;

class Output extends Core {
    static $instance = null;

    public $className = 'Core';
    public $refreshPage;
    public $data;
    public $element;
    public $model;
    public $fo;        // 'FO' Formatting Object (Type of XML)
    public $ui;
    public $session;

    function __construct() {
        if (!isset($this->output)) {
            $this->output = new \stdClass();
        }
    }

    function __destruct(){
        unset($this);
    }

    public static function getInstance() {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}
