<?php
namespace core\system;
use core\Heepp;
use core\extension\cache\cache;

class render {
    public static $string = [];
    public static $json   = [];
    public static $html   = [];
    public static $array  = [];
    public static $object = [];

    public static function json($json) {
        self::$json[] = $json;
    }

    public static function html($html) {
        if (is_array($html)) {
            foreach($html as $htm) {
                if (strpos($htm,'<html') === 0) {
                    self::$html[] = '<!DOCTYPE html>'.$htm;
                }
            }
        } else {
            if (strpos($html,'<html') === 0) {
                self::$html[] = '<!DOCTYPE html>'.$html;
            }
        }
    }

    public static function array($array) {
        self::$array[] = $array;
    }

    public static function object($object) {
        self::$object[] = $object;
    }

    public static function string($output) {
        switch(gettype($output)) {
            case 'boolean':
            case 'integer':
            case 'double':
                switch($output) {
                    case true:
                        $string = 'true';
                    break;
                    case false:
                        $string = 'true';
                    break;
                    default:
                        $string = (string)$output;
                    break;
                }
            break;
            case 'NULL':
                $string = 'null';
            break;
            case 'array':
                $string = self::recursiveImplode($output);
            break;
            case 'object':
                $string = serialize($output);
            break;
            default:
                $string = (string)$output;
            break;
        }
        self::$string[] = $string;
    }

    public static function recursiveImplode($array,$glue = ' ') {
        foreach($array as $key => $value) {
            if (is_array($key)) {
                self::$string .= implode($glue,$key);
            } else {
                self::$string .= self::string($key);
            }

            self::$string .= ' = ';
            if (is_array($value)) {
                self::$string .= implode($glue,$value);
            } else {
                self::$string .= self::string($key);
            }
        }
    }

    public static function output() {
        $output = (object)[];
        $typesRendered = [];
        if (count(self::$string) > 0) {
            $typesRendered[] = 'string';
            $output->string = '';
            foreach(self::$string as $str) {
                $output->string .= $str;
            }
        }

        if (count(self::$json) > 0) {
            foreach(self::$json as $json) {
                $typesRendered[] = 'array';
                $output->array = [];
                array_merge_recursive($output->array,json_decode(json_encode($json),true));
            }
        }

        if (count(self::$array) > 0) {
            if (!in_array('array',$typesRendered)) {
                $typesRendered[] = 'array';
            }

            foreach(self::$array as $array) {
                if (!isset($output->array)) {
                    $output->array = [];
                }
                $output->array = array_merge_recursive($output->array,$array);
            }
        }

        if (count(self::$html) > 0) {
            $typesRendered[] = 'html';
            $output->html = '';
            foreach(self::$html as $html) {
                $output->html .= $html;
            }
        }

        if (count(self::$array) > 0) {
            if (!in_array('array',$typesRendered)) {
                $typesRendered[] = 'array';
            }
            $output->object = [];
            foreach(self::$object as $object) {
                if (!isset($output->array)) {
                    $output->array = [];
                }
                array_merge_recursive($output->array,json_decode(json_encode($object),true));
            }
        }

        if (count($typesRendered) == 1) {
            switch($typesRendered[0]) {
                case 'string':
                    Heepp::contentType(env('text.header'));
                    echo $output->string;
                break;
                case 'array':
                    header('Content-Type: text/html');
                    Heepp::contentType(env('json.header'));
                    echo json_encode($output->array);
                break;
                case 'html':
                    Heepp::contentType(env('app.header.type'));
                    echo $output->html;
                break;
            }
        } else {
            $content = (object)[];
            foreach($typesRendered as $type) {
                switch($type) {
                    case 'string':
                        $content->string =  $output->string;
                    break;
                    case 'array':

                        $content->array = json_encode($output->array);
                    break;
                    case 'html':
                        $content->html = $output->html;
                    break;
                }
            }
            Heepp::contentType(env('json.header'));
            echo json_encode($content);
        }
    }
}
