<?php
use core\Heepp;

function inputSet($inputKey = null) {
    return (new Heepp())->inputSet($inputKey);
}

function inputEmpty($inputKey = null) {
    return empty($inputKey);
    //return (new \core\Heepp())->inputEmpty($inputKey);
}

function input($inputKey = null,$default = null) {
    return (new Heepp())->input($inputKey,$default);
}

function setInput($inputKey,$value,$method = 'get') {
    if ($method === 'get') {
        return $_GET[$inputKey] = $value;
    }
    return $_POST[$inputKey] = $value;
}

function session($dotName = null,$value = null) {
    return (new Heepp())->session($dotName,$value);
}

function sessionSet($dotName) {
    return (new Heepp())->sessionKeyExist($dotName);
}

function data($dotName,$value = null,$data = null) {
    return Heepp::data($dotName,$value,$data);
}

function urlExists($file) {
    $file_headers = [];
    if ($file) {
        $file_headers = @get_headers($file);
    }

    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403') {
        return false;
    }
    return true;
}

function currentUrl() {
    return (isset($_SERVER[env('request.scheme')]) ? 'https' : 'http') . '://'.env('http.host').env('request.uri');
}

function url($relPath) {
    $filePathName = realpath($relPath);
    $filePath = realpath(dirname($relPath));
    $basePath = realpath($_SERVER['DOCUMENT_ROOT']);
    
    // can not create URL for directory lower than DOCUMENT_ROOT
    if (strlen($basePath) > strlen($filePath)) {
        return '';
    }
    $url = 'http://' . $_SERVER['HTTP_HOST'] . substr($filePathName, strlen($basePath));
    return str_replace(DIRECTORY_SEPARATOR,'/',$url);
}

function base64urlEncode($utl) {
    return rtrim(strtr(base64_encode($utl), '+/', '-_'), '='); 
} 

function base64urlDecode($utl) {
    return base64_decode(str_pad(strtr($utl, '-_', '+/'), strlen($utl) % 4, '=', STR_PAD_RIGHT)); 
}

// Get the base http request path
function baseUrl($atRoot = false,$atCore = false,$parse = false) {
    if (isset($_SERVER['HTTP_HOST'])) {
        $http     = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
        $hostname = $_SERVER['HTTP_HOST'];
        $dir      = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        // Adding the following to replace 'core' with project name

        $dir      = str_replace(basename($dir),env('project.name'),$dir);
        $core     = preg_split('@/@',str_replace($_SERVER['DOCUMENT_ROOT'],'',realpath(__DIR__)),null, PREG_SPLIT_NO_EMPTY);
        $core     = $core[0];
        $tmplt    = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
        $end      = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
        $base_url = sprintf($tmplt,$http,$hostname,$end);
    } else {
        $base_url = 'http://localhost/';
    }
    if ($parse) {
        $base_url = parse_url($base_url);
        if (isset($base_url['path'])) {
            if ($base_url['path'] == '/') {
                $base_url['path'] = '';
            }
        }
    }
    return $base_url;
}
// Setup Special Paths
function stripURI($url) {
    $explode = explode('?',$url);
    return str_replace('index.php','',$explode[0]);
}

/* Checks a string to see if it is a valid url address and appends http:// if it doesn't have */
function cleanURL($url, $clean = true) {
    if (strpos($url, '://') <= 0) {
        $url = 'http://'.$url;
    }
    return $clean ? clean($url) : $url;
}

/**
 * Verify the syntax of the given URL.
 *
 * @access public
 * @param $url The URL to verify.
 * @return boolean
 */
function isValidUrl($url) {
    if (str_starts_with($url, '/')) {
        return true;
    }
    return preg_match(URL_FORMAT, $url);
}

/**
 * Redirect to specific URL (header redirection)
 *
 * @access public
 * @param string $to Redirect to this URL
 * @param boolean $die Die when finished
 * @return void
 */
function redirectTo($to,$die = true) {
    $to = trim($to);
    if (strpos($to, '&amp;') !== false) {
        $to = str_replace('&amp;', '&', $to);
    }
    if (is_ajax_request()) {
        $to = make_ajax_url($to);
    }
    header('Location: ' . $to);
    if ($die) {
        die();
    }
}

/**
 * Redirect to referer
 *
 * @access public
 * @param string $alternative Alternative URL is used if referer is not valid URL
 * @return null
 */
function redirectToReferer($alternative = nulls) {
    $referer = get_referer();
    /** @noinspection SuspiciousBinaryOperationInspection */
    if (true || !is_valid_url($referer)) {
        if (is_ajax_request()) {
            $alternative = make_ajax_url($alternative);
        }
        redirect_to($alternative);
    } else {
        if (is_ajax_request()) {
            $referer = make_ajax_url($referer);
        }
        redirect_to($referer);
    }
}

/**
 * Return referer URL
 *
 * @param string $default This value is returned if referer is not found or is empty
 * @return string
 */
function getReferer($default = null) {
    return array_var($_SERVER,'HTTP_REFERER',$default);
}

/**
 * This function will strip slashes if magic quotes is enabled so
 * all input data ($_GET, $_POST, $_COOKIE) is free of slashes
 *
 * @access public
 * @param void
 * @return null
 */
function fixInputQuotes() {
    if (get_magic_quotes_gpc()) {
        arrayStripslashes($_GET);
        arrayStripslashes($_POST);
        arrayStripslashes($_COOKIE);
    }
}

/**
 * escapes this characters: & ' " < >
 */
function escapeSLIM($rawSLIM) {
    return rawurlencode($rawSLIM);
}

/**
 * unescapes: &amp; &#39; &quot; &lt; &gt;
 */
function unescapeSLIM($encodedSLIM) {
    return rawurldecode($encodedSLIM);
}

/**
 *
 * @return the real Clients IP
 */
function getIPAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {               //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * Make a request
 *
 * @param string $url
 * @param string $method ('GET','POST')
 * @param array  $data
 * @param string $additional_headers
 * @param bool   $followRedirects
 *
 * @return array
 */
function httpRequest($url,$method = 'GET',$data = NULL,$additional_headers = NULL,$followRedirects = true,$async = false) {
    $original_data = $data;
    $header = '';
    $body = '';
    # in compliance with the RFC 2616 post data will not redirected
    $method = strtoupper($method);
    $url_parsed = @parse_url($url);
    if (!@$url_parsed['scheme']) {
        $url_parsed = @parse_url('http://' . $url);
    }
    extract($url_parsed);
    if (!is_array($data)) {
        $data = NULL;
    } else {
        $ampersand = '';
        $temp = NULL;
        foreach ($data as $k => $v) {
            $temp .= $ampersand . urlencode($k) . '=' . urlencode($v);
            $ampersand = '&';
        }
        $data = $temp;
    }
    if (!isset($port)) {
        $port = 80;
    }
    if (!isset($path)) {
        $path = '/';
    }
    if (($method == 'GET') and ( $data)) {
        $query = (@$query) ? '&' . $data : '?' . $data;
    }
    if (@isset($query)) {
        $path .= '?' . $query;
    }
    $out = "$method $path HTTP/1.0\r\n";
    $out .= "Host: $host\r\n";
    if ($method == 'POST') {
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-length: " . @strlen($data) . "\r\n";
    }
    $out .= (@$additional_headers) ? $additional_headers : '';
    $out .= "Connection: Close\r\n\r\n";
    if ($method == 'POST') {
        $out .= $data . "\r\n";
    }
    if (!$fp = @fsockopen($host, $port, $es, $en, 5)) {
        $err = error_get_last();
        Logger::log('Error on fsockopen: ' . $err["message"] . $err["file"] . $err["line"]);
        return false;
    }
    fwrite($fp, $out);

    if ($async) {
        // don't read from the socket connection if the request is asynchronic
        fclose($fp);
        return;
    } else {
        $result = '';
        while (!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        // split the result header from the content
        $result = explode("\r\n\r\n", $result, 2);

        $header = isset($result[0]) ? $result[0] : '';
        $body = isset($result[1]) ? $result[1] : '';

        $headers = explode("\r\n", $header);
        $status = $headers[0];

        if ($followRedirects) {
            foreach ($headers as $hline) {
                if (str_starts_with($hline, "Location:")) {
                    $url = trim(str_replace("Location:", "", $hline));
                    return HttpRequest($url, $method, $original_data, $additional_headers, $followRedirects);
                }
            }
        }
    }
    return [
        'head'   => trim($header),
        'body'   => trim($body),
        'status' => $status
    ];
}

function noDiacritics($string) {
    //cyrylic transcription
    $cyrylicFrom = ['А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'];
    $cyrylicTo = ['A','B','W','G','D','Ie','Io','Z','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','Ch','C','Tch','Sh','Shtch','','Y','','E','Iu','Ia','a','b','w','g','d','ie','io','z','z','i','j','k','l','m','n','o','p','r','s','t','u','f','ch','c','tch','sh','shtch','','y','','e','iu','ia'];

    $from = ['Á','À','Â','Ä','Ă','Ā','Ã','Å','Ą','Æ','Ć','Ċ','Ĉ','Č','Ç','Ď','Đ','Ð','É','È','Ė','Ê','Ë','Ě','Ē','Ę','Ə','Ġ','Ĝ','Ğ','Ģ','á','à','â','ä','ă','ā','ã','å','ą','æ','ć','ċ','ĉ','č','ç','ď','đ','ð','é','è','ė','ê','ë','ě','ē','ę','ə','ġ','ĝ','ğ','ģ','Ĥ','Ħ','I','Í','Ì','İ','Î','Ï','Ī','Į','Ĳ','Ĵ','Ķ','Ļ','Ł','Ń','Ň','Ñ','Ņ','Ó','Ò','Ô','Ö','Õ','Ő','Ø','Ơ','Œ','ĥ','ħ','ı','í','ì','i','î','ï','ī','į','ĳ','ĵ','ķ','ļ','ł','ń','ň','ñ','ņ','ó','ò','ô','ö','õ','ő','ø','ơ','œ','Ŕ','Ř','Ś','Ŝ','Š','Ş','Ť','Ţ','Þ','Ú','Ù','Û','Ü','Ŭ','Ū','Ů','Ų','Ű','Ư','Ŵ','Ý','Ŷ','Ÿ','Ź','Ż','Ž','ŕ','ř','ś','ŝ','š','ş','ß','ť','ţ','þ','ú','ù','û','ü','ŭ','ū','ů','ų','ű','ư','ŵ','ý','ŷ','ÿ','ź','ż','ž','&amp;','&'];
    $to = ['A','A','A','A','A','A','A','A','A','AE','C','C','C','C','C','D','D','D','E','E','E','E','E','E','E','E','G','G','G','G','G','a','a','a','a','a','a','a','a','a','ae','c','c','c','c','c','d','d','d','e','e','e','e','e','e','e','e','g','g','g','g','g','H','H','I','I','I','I','I','I','I','I','IJ','J','K','L','L','N','N','N','N','O','O','O','O','O','O','O','O','CE','h','h','i','i','i','i','i','i','i','i','ij','j','k','l','l','n','n','n','n','o','o','o','o','o','o','o','o','o','R','R','S','S','S','S','T','T','T','U','U','U','U','U','U','U','U','U','U','W','Y','Y','Y','Z','Z','Z','r','r','s','s','s','s','B','t','t','b','u','u','u','u','u','u','u','u','u','u','w','y','y','y','z','z','z','and','and'];

    $from = array_merge($from,$cyrylicFrom);
    $to = array_merge($to,$cyrylicTo);
    return str_replace($from, $to, $string);
}

function slugify($string,$maxlen = 0) {
    $newStringTab = array();
    $string = strtolower(trim(noDiacritics(htmlspecialchars_decode($string))));
    if (function_exists('str_split')) {
        $stringTab = str_split($string);
    } else {
        $stringTab = myStrSplit($string);
    }
    $numbers = ['0','1','2','3','4','5','6','7','8','9','-'];
    foreach ($stringTab as $letter) {
        if (in_array($letter, range('a','z')) || in_array($letter, $numbers)) {
            $newStringTab[] = $letter;
        } elseif ($letter == ' ') {
            $newStringTab[] = '-';
        }
    }
    if (count($newStringTab)) {
        $newString = implode($newStringTab);
        if ($maxlen > 0) {
            $newString = substr($newString, 0, $maxlen);
        }
        $newString = removeDuplicates('--', '-', $newString);
    } else {
        $newString = '';
    }
    return $newString;
}

function myStrSplit($string) {
    $return_array = array();
    for ($i = 0,$iMax = strlen($string); $i < $iMax; $i++) {
        $return_array[$i] = $string[$i];
    }
    return $return_array;
}

function removeDuplicates($sSearch, $sReplace, $sSubject) {
    $i = 0;
    do {
        $sSubject = str_replace($sSearch, $sReplace, $sSubject);
        $pos = strpos($sSubject, $sSearch);
        $i++;
        if ($i > 100) {
            die('removeDuplicates() loop error');
        }

    } while ($pos !== false);
    return $sSubject;
}
