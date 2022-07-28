<?php
namespace core;

class Output {
    public static $instance;
    public $className = '';
    public $refreshPage;
    public $data;
    public $element;
    public $model;
    public $ui;
    public $session;

    public function __construct() {
        if (!isset($this->output)) {
            $this->output = new \stdClass();
        }
    }

    public static function getInstance() {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
}
