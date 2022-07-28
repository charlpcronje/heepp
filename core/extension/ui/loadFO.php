<?php
namespace core\extension\ui;

class LoadFO extends \core\Heepp {
    function __construct() {
        parent::__construct();
    }

    public function loadXML($view,$controller = null,$target = null) {
        $view = str_replace('-','/',$view);
        $controller = str_replace('-','/',$controller);
        if (!empty($controller) && $controller != 'undefined') {
            $params = explode('/',$controller,2);
            $controller = $params[0];

            $class = $controller;
            $coreClass = 'Heepp\\'.$class;

            if (class_exists($class)) {
                $object = new $class;
            } elseif (class_exists($coreClass)) {
                $object = new $coreClass;
            } else {
                $this->setError("controller: '".$controller."' does not exist");
            }

            //Check if any parameter is set after the controller
            if (!empty($params[1])) {
                $qrysrt = $params[1];
                $result = explode('/',$qrysrt,2);
                $function = $result[0];
                if (isset($result[1])) {
                    $param = $result[1];
                    $params = explode('/',$param);
                }

                if (method_exists($object,'hasAccess')) {
                    if ($object->hasAccess($function)) {
                        if (class_exists($controller)) {
                            $obj = new \ReflectionMethod($controller, $function);
                            $obj->invokeArgs($object, $params);
                        } else {
                            $controller = 'Heepp\\'.$controller;
                            $obj = new \ReflectionMethod($controller, $function);
                            $obj->invokeArgs($object, $params);
                        }
                    }
                } else {
                    $obj = new \ReflectionMethod($controller, $function);
                    $obj->invokeArgs($object, $params);
                }
            }
            $fo = new coreFO($view,$object);
        } else {
            $fo = new coreFO($view);
        }
        if (!empty($target)) {
            $target = str_replace('*','#',$target);
            $this->setHtml($target, $fo->html);
        } else {
            $this->setHtml('body',$fo->html);
        }
    }
}
