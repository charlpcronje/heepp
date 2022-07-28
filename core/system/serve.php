<?php
namespace core\system;
use core\Heepp;
use core\extension\ui\view;

class serve {
    public static $string = '';

    // $type = 'text/plain'
    public static function string($string) {
        if (strpos($string,'views/') === 0) {
            Heepp::contentType(env(app.header.type));
            $string = view::mold($string);
        } elseif(strpos($string,'@') !== false) {
            $controllerAndMethod = explode('@',$string);
            $controller = $controllerAndMethod[0];
            $method = $controllerAndMethod[1];
            $reflect = new \ReflectionMethod('\\'.$controller,$method);
            $string = $reflect->invokeArgs(new $controller(),(array)route::$routeOptions->data);
        }
        return self::getType($string);
    }

    public static function object($object) {
        if(isset($object->controller)) {
            $controller = $object->controller;
            $method = 'index';
            if (isset($object->method)) {
                $method = $object->method;
            }
            $data = (object)[];
            if (isset($object->data)) {
                if (isClosure($object->data)) {
                    $object->data = $object->data->__invoke();
                } elseif(is_array($object->data)) {
                    $data = $object->data;
                }
            }
            $obj = new $controller;
            return self::getType(call_user_func_array([$obj,$method],$data));
        }

        /* Check for "data" key */
        $data = (object)[];
        if (isset($object->data)) {
            if (isClosure($object->data)) {
                $object->data = $object->data->__invoke();
            } elseif(is_array($object->data)) {
                $data = $object->data;
            }
        }

        /* Check for "view" key */
        if (isset($object->view)) {
            $view = $object->view;
            $path = env('project.path');
            if (isset($object->path)) {
                $path = $object->path;
            }
            return [
                'html' => self::view($view,$data,$path)
            ];
        }

        /* Check for "fragment" key */
        if (isset($object->fragment)) {
            $view = $object->fragment;
            $data = (object)[];
            $path = env('project.path');
            if ($object->path) {
                $path = $object->path;
            }
            return [
                'html' => self::view($view,$data,$path,true)
            ];
        }
    }

    public static function html($html) {
        return [
            'html' => $html
        ];
    }

    public static function getType($result) {
        /* JSON
        |------ */
        if (is_string($result) && isJson($result)) {
            return [
                'json' => $result
            ];
        }

        /* HTML
        |------ */
        if (!is_array($result) && isHtml($result)) {
            return [
                'html' => $result
            ];
        }

        /* ARRAY
        |------- */
        if (is_array($result)) {
            return [
                'array' => $result
            ];
        }

        /* STD CLASS OBJ
        |--------------- */
        if (is_object($result)) {
            return [
                'object' => $result
            ];
        }

        /* STRING
        |-------- */
        if (is_string($result)) {
            return [
                'string' => $result
            ];
        }

        /* ELSE
        |------ */
        return [
            'html' => $result
        ];
    }

    public static function closure($closure,$type = 'text/html') {
        if (!self::httpHeaderExist('Content-type')) {
            Heepp::contentType($type);
        }
        return self::getType($closure->__invoke());
    }

    public static function view($view,$data = null,$path = null,$isFragment = false) {
        if (!isset($path)) {
            $path = env('project.path');
        }
        if (!self::httpHeaderExist('Content-type')) {
            Heepp::contentType('text/html');
        }
        if (is_array($data) && count($data) > 0) {
            foreach($data as $key => $value) {
                Heepp::data($key,$value);
            }
        }
        if ($isFragment) {
            return [
                'html' => view::mold($view,$path)
            ];
        }
        return [
            'html' => view::mold($view,$path)
        ];
    }

    public static function headersToArray() {
        $httpHeaders = headers_list();
        $headers = [];
        foreach($httpHeaders as $header) {
            $keyValue = explode(':',$header,2);
            $headers[$keyValue[0]] = $keyValue[1];
        }
        return $headers;
    }

    public static function httpHeaderExist($header) {
        $headers = self::headersToArray();
        if (array_key_exists($header,$headers)) {
            return true;
        }
        return false;
    }
}
