<?php

/**
 * Replace first $search_for with $replace_with in $in. If $search_for is not found
 * original $in string will be returned...
 * @access public
 *
 * @param $search
 * @param $replace
 * @param $subject
 *
 * @return string
 */
function strReplaceFirst($search,$replace,$subject) {
    $search = '/'.preg_quote($search,'/').'/';
    return preg_replace($search,$replace,$subject, 1);
}

function isNonEmptyStr($var) {
    return is_string($var) && strlen($var) > 0;
}

function isJson($string) {
    if (is_string($string)) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    return false;
}

function isHtml($string){
    return $string !== strip_tags($string);
}
/**
 * Convert entities back to valid characteds
 * @param string $escaped_string
 * @return string
 */
function undoHTMLspecialChars($escaped_string) {
    $search = array('&amp;', '&lt;', '&gt;');
    $replace = array('&', '<', '>');
    return str_replace($search,$replace,$escaped_string);
}

/**
 * This function will return $str as an array
 * @param string $str
 * @return array
 */
function stringToArray($str) {
    if (!is_string($str) || (strlen($str) == 0)) {
        return [];
    }
    $result = [];
    foreach($str as $iValue) {
        $result[] = $iValue;
    }

    return $result;
}

/**
 * String starts with something
 * This function will return true only if input string starts with
 * niddle
 * @param string $string Input string
 * @param string $niddle Needle string
 * @return boolean
 */
function strStartsWith($string,$niddle) {
    return 0 === strpos($string,$niddle);
}

/**
 * String ends with something
 * This function will return true only if input string ends with
 * niddle
 *
 * @param string $string Input string
 * @param $needdle
 * @return boolean
 */
function strEndsWith($string,$needdle) {
    return substr($string,strlen($string) - strlen($needdle),strlen($needdle)) == $needdle;
}

/**
 * Return path with trailing slash
 * @param string $path Input path
 * @return string Path with trailing slash
 */
function withSlash($path) {
    return str_ends_with($path,'/') ? $path : $path.'/';
}

/**
 * Remove trailing slash from the end of the path (if exists)
 * @param string $path File path that need to be handled
 * @return string
 */
function withoutSlash($path) {
    return strEndsWith($path,'/') ? substr($path, 0,strlen($path) - 1) : $path;
}

/**
 * Check if selected email has valid email format
 * @param string $user_email Email address
 * @return boolean
 */
function isValidEmail($user_email) {
    $chars = EMAIL_FORMAT;
    if (strstr($user_email,'@') && strstr($user_email,'.')) {
        return (boolean) preg_match($chars,$user_email);
    } else {
        return false;
    }
}

/**
 * Prepends a backslash before single quotes
 * @param $text
 * @return string
 */
function escapeSingleQuotes($text) {
    return str_replace("'", "\\'", $text);
}

function escapeHTMLWhitespace($html) {
    return str_replace(array("\r\n","\r","\n",'  ',"\t",'  ','<br/> '),array('<br/>','<br/>','<br/>','&nbsp; ','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','&nbsp; ','<br/>&nbsp;'),$html);
}