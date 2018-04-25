<?php
namespace core\element\base;
use core\Element;

 /* with an element */
class invoke extends Element {
    public $class;
    public $method;
    public $methodParamNames = [];
    public $methodParams     = [];

    public function __set($name,$value) {
        if (count($this->methodParamNames) == 0) {
            $this->methodParamNames = getMethodParams($this->class,$this->method);
        }
        if (in_array($name,$this->methodParamNames)) {
            $this->methodParams[$name] = $value;
        }
    }

    public function render() {
        $method = $this->method;
        $response = call_user_func_array([$this->class,$this->method],$this->methodParams);
        //$reflection = new \ReflectionMethod($this->class,$this->method);
        //$methodResponse = $reflection->invokeArgs($this->class,$this->methodParams);
        return $this->child;
    }
}
