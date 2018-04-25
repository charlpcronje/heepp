<?php
use core\Heepp;

function dd($var) {
    $callingClass = getCallingClass();
    if (empty($callingClass)) {
        $callingClass = 'Global Scope';
    }
    echo 'Dumping and Die Called From: <strong>'.$callingClass."</strong> \r\n\r\n <br/><br/>";
    var_dump($var);
    die;
}

function ddc($var) {
    echo 'Dumping and Continue Called From: <strong>'.getCallingClass()."</strong> \r\n\r\n <br/><br/>";
    var_dump($var);
    echo '</pre>';
}

function pd($var) {
    $callingClass = getCallingClass();

    if (empty($callingClass)) {
        $trace = debug_backtrace();
        $callingClass = 'File: "'.$trace[0]['file'].'" | Line: "'.$trace[0]['line'].'""';
    }

    echo 'Print and Die Called From: <strong>'.$callingClass."</strong> \r\n\r\n <br/><br/> <pre>";
    print_r($var);
    echo '</pre>';
    die;
}

function env($key = null,$value = null,$default = null) {
    if (isset($value)) {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        //if (isset($_SESSION)) {
            Heepp::data('env.'.$key,$value);
        //}
    }

    if (!isset($key) && !isset($default)) {
        return array_merge($_SERVER,$_ENV);
    }
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    if (isset($default)) {
        return $default;
    }
}

function pdc($var) {
    $callingClass = getCallingClass();

    if (empty($callingClass)) {
        $trace = debug_backtrace();
        $callingClass = 'File: "'.$trace[0]['file'].'" | Line: "'.$trace[0]['line'].'""';
    }

    echo 'Print, Dump and Continue Called From: '.$callingClass." \r\n\r\n ";
    print_r($var);
}

function is_closure($var) {
    return is_object($var) && ($var instanceof Closure);
}

function getMethodParams($class,$method) {
    $ReflectionMethod =  new \ReflectionMethod($class, $method);
    $params = $ReflectionMethod->getParameters();
    $paramNames = array_map(function( $item ){
        return $item->getName();
    }, $params);
    return $paramNames;
}

function getFunctionParams($function) {
    $reflectionFunction =  new \ReflectionFunction($function);
    $params = $reflectionFunction->getParameters();
    $paramNames = array_map(function( $item ){
        return $item->getName();
    }, $params);
    return $paramNames;
}

function classProperties(&$object) {
    $className = get_class($object);
    $ref = new \ReflectionClass($className);
    $ownProps = array_filter($ref->getProperties(), function($property) use ($className) {
        return $property->class == $className; 
    });
    
    $returnProps = new stdClass();
    foreach($ownProps as $prop) {
        
        $returnProps->{$prop->name} = $object->{$prop->name};
    }
    return $returnProps;
}

function base64EncodeImage($filename = string,$filetype = string) {
    if ($filename) {
        $imgbinary = fread(fopen($filename, "r"), filesize($filename));
        return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
    }
}

function getCallingClass() {
    //get the trace
    $trace = debug_backtrace();

    // Get the class that is asking for who awoke it
    if (isset($trace[1]['class'])) {
        $class = $trace[1]['class'];
    } else {
        $class = $trace[1]['class'] = 'Static';
    }


    // +1 to i cos we have to account for calling this function
    for ($i=1,$iMax = count($trace); $i< $iMax; $i++ ) {
        if (isset($trace[$i])) { // is it set?
             if ($class != @$trace[$i]['class']) {// is it a different class
                 return @$trace[$i]['class'];
             }
        }
    }
}

// Find Class Ancestors (Parents and Parents of Parents)
function getAncestors($class) {
  for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class);
  return $classes;
}

function dotNotation($string) {
    return str_replace(['-','\\','/'],'.',$string);
}

/**
 * Check if $object is valid $class instance
 *
 * @access public
 * @param mixed $object Variable that need to be checked against className
 * @param string $class ClassName
 * @return null
 */
function isInstanceOf($object, $class) {
    return $object instanceof $class;
}

/**
 * This function will return clean variable info
 *
 * @param mixed $var
 * @param string $indent Indent is used when dumping arrays recursivly
 * @param string $indent_close_bracet Indent close bracket param is used
 *   internaly for array output. It is shorter that var indent for 2 spaces
 * @return null
 */
function cleanVarInfo($var, $indent = '&nbsp;&nbsp;', $indent_close_bracet = '') {
    if (is_object($var)) {
        return 'Object (class: '.get_class($var).')';
    } elseif (is_resource($var)) {
        return 'Resource (type: '.get_resource_type($var).')';
    } elseif (is_array($var)) {
        $result = 'Array (';
        if (count($var)) {
            foreach ($var as $k => $v) {
                $k_for_display = is_integer($k) ? $k : "'" . clean($k) . "'";
                $result .= "\n".$indent.'['.$k_for_display.'] => ' .clean_var_info($v,$indent.'&nbsp;&nbsp;',$indent_close_bracet.$indent);
            }
        }
        return $result."\n$indent_close_bracet)";
    } elseif (is_int($var)) {
        return '(int)'.$var;
    } elseif (is_float($var)) {
        return '(float)'.$var;
    } elseif (is_bool($var)) {
        return $var ? 'true' : 'false';
    } elseif (is_null($var)) {
        return 'NULL';
    } else {
        return "(string) '".clean($var)."'";
    }
}

/**
 * Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
 * This function was taken from punBB codebase <http://www.punbb.org/>
 *
 * @param string $str
 * @return string
 */
function clean($str) {
    $str = preg_replace('/&(?!#[0-9]+;)/s','&amp;',$str);
    $str = str_replace(array('<', '>', '"'),array('&lt;','&gt;','&quot;'),$str);

    return $str;
}

/**
 * This function will return true if $str is valid function name (made out of alpha numeric characters + underscore)
 *
 * @param string $str
 * @return boolean
 */
function isValidFunctionName($str) {
    $check_str = trim($str);
    if ($check_str == '') {
        return false; // empty string
    }

    $first_char = substr_utf($check_str,0,1);
    if (is_numeric($first_char)) {
        return false; // first char can't be number
    }

    return (boolean) preg_match("/^([a-zA-Z0-9_]*)$/",$check_str);
}

/**
 * Check if specific string is valid sha1() hash
 *
 * @param string $hash
 * @return boolean
 */
function isValidHash($hash) {
    return ((strlen($hash) == 32) || (strlen($hash) == 40)) && (boolean) preg_match("/^([a-f0-9]*)$/", $hash);
}

/**
 * Return variable from hash (associative array). If value does not exists
 * return default value
 *
 * @access public
 * @param array $from Hash
 * @param string $name
 * @param mixed $default
 * @return mixed
 */
function arrayVAR(&$from,$name,$default = null) {
    if (is_array($from)) {
        return isset($from[$name]) ? $from[$name] : $default;
    }
    return $default;
}

/**
 * This function will return ID from array variables. Default settings will get 'id'
 * variable from $_GET. If ID is not found function will return NULL
 *
 * @param string $var_name Variable name. Default is 'id'
 * @param array $from Extract ID from this array. If NULL $_GET will be used
 * @param mixed $default Default value is returned in case of any error
 * @return integer
 */
function getID($var_name = 'id',$from = null,$default = null) {
    $var_name = trim($var_name);
    if ($var_name == '') {
        return $default; // empty varname?
    }
    if (is_null($from)) {
        $from = $_GET;
    }
    if (!is_array($from)) {
        return $default; // $from is array?
    }
    if (!is_valid_function_name($var_name)) {
        return $default; // $var_name is valid?
    }

    $value = array_var($from, $var_name, $default);
    return is_numeric($value) ? (integer) $value : $default;
}

/**
 * This function returns true if the specified value is found in the CSV formatted string
 *
 * @param string $csv
 * @param $value
 * @return bool
 */
function inCSV($csv, $value) {
    $arr = explode(',', $csv);
    for ($i = 0,$iMax = count($arr); $i < $iMax; $i++) {
        if ($value == trim($arr[$i])) {
            return true;
        }
    }
    return false;
}

/**
 * Flattens the array. This function does not preserve keys, it just returns
 * array indexed form 0 .. count - 1
 *
 * @access public
 * @param array $array If this value is not array it will be returned as one
 * @return array
 */
function arrayFlat($array) {
    if (!is_array($array)) {                // Not an array
        return array($array);
    }
    $result = array();                      // Prepare result

    foreach ($array as $value) {            // Loop elemetns
        if (is_array($value)) {             // Subelement is array? Flat it
            $value = array_flat($value);
            foreach ($value as $subvalue) {
                $result[] = $subvalue;
            }
        } else {
            $result[] = $value;
        }
    }
    return $result;
}

/**
 * This function will return max upload size in bytes
 *
 * @param void
 * @return integer
 */
function getMaxUploadSize() {
    $max = min(
        php_config_value_to_bytes(ini_get('upload_max_filesize')),php_config_value_to_bytes(ini_get('post_max_size'))
    );
    Hook::fire('max_upload_size',null,$max);
    return $max;
}

/**
 * This function will return max execution time in seconds.
 *
 * @param void
 * @return integer
 */
function getMaxExecution_time() {
    $max = ini_get("max_execution_time");
    if (!$max) {
        $max = 0;
    }
    return $max;
}

/**
 * Convert PHP config value (2M, 8M, 200K...) to bytes
 *
 * This function was taken from PHP documentation
 *
 * @param string $val
 * @return integer
 */
function phpConfigValueToBytes($val) {
    $val = trim($val);
    if ($val == "") {
        return 0;
    }
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

// ==========================================================
//  POST and GET
// ==========================================================



/**
 * This function will walk recursivly thorugh array and strip slashes from scalar values
 *
 * @param array $array
 * @return null
 */
function arrayStripslashes(&$array) {
    if (!is_array($array)) {
        return;
    }
    foreach ($array as $k => $v) {
        if (is_array($array[$k])) {
            array_stripslashes($array[$k]);
        } else {
            $array[$k] = stripslashes($array[$k]);
        }
    }
    return $array;
}

/**
 * Generates a random id to be used as id of HTML elements.
 * It does not guarantee the uniqueness of the id, but the probability
 * of generating a duplicate id is very small.
 *
 */
function genId() {
    static $ids = array();
    do {
        $time = time();
        $rand = rand(0, 1000000);
        $id = "og_" . $time . "_" . $rand;
    } while (array_var($ids, $id, false));
    $ids[$id] = true;
    return $id;
}

function zipSupported() {
    return class_exists('ZipArchive',false);
}

function pluginSort($a,$b) {
    if (isset($a ['order']) && isset($b ['order'])) {
        if ($a ['order'] == $b ['order']) {
            return 0;
        }
        return ($a ['order'] < $b ['order']) ? - 1 : 1;
    } elseif (isset($a ['order'])) {
        return - 1;
    } elseif (isset($b ['order'])) {
        return 1;
    } else {
        return strcasecmp($a ['name'], $b ['name']);
    }
}
