<?php

function getFreeDiskSpace() {
    $bytes = disk_free_space(".");
    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
    $base = 1024;
    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
    //$response = $bytes;
    $response =  sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
    return $response;
}

function remoteFilemtime($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    curl_setopt($ch, CURLOPT_FILETIME, TRUE);
    $data = curl_exec($ch);
    $filetime = curl_getinfo($ch, CURLINFO_FILETIME);
    curl_close($ch);
    return $filetime;
}

function latestVersion($file_name) {
    return $file_name . "?" . filemtime($_SERVER['DOCUMENT_ROOT'] . $file_name);
}

function load($file) {
    return file_get_contents($file);
}

function loadJSON($file,$assoc = false) {
    return json_decode(file_get_contents($file),$assoc);
}

function loadXML($xmlFile,$flags = null) {
    if (isset($flags)) {
        return simplexml_load_string(file_get_contents($xmlFile),'SimpleXMLElement',$flags);
    }
    return simplexml_load_string(file_get_contents($xmlFile),'SimpleXMLElement');
}

function urlToPath($url) {
    return str_replace(env('base.url'),env('project.path'),$url);
}

function pathToUrl($path) {
    return str_replace(env('project.path'),env('base.url'),$path);
}

function remoteFileSize($url) {
    static $regex = '/^Content-Length: *+\K\d++$/im';
    if (!$fp = @fopen($url, 'rb')) {
        return false;
    }
    if (
        isset($http_response_header) &&
        preg_match($regex, implode("\n", $http_response_header), $matches)
    ) {
        return (int)$matches[0];
    }
    return strlen(stream_get_contents($fp));
}

function FileSizeConvert($bytes){
    $bytes = floatval($bytes);
    $arBytes = [
        0 => [
            "UNIT" => "TB",
            "VALUE" => pow(1024, 4)
        ],
        1 => [
            "UNIT" => "GB",
            "VALUE" => pow(1024, 3)
        ],
        2 => [
            "UNIT" => "MB",
            "VALUE" => pow(1024, 2)
        ],
        3 => [
            "UNIT" => "KB",
            "VALUE" => 1024
        ],
        4 => [
            "UNIT" => "B",
            "VALUE" => 1
        ]
    ];

    foreach($arBytes as $arItem) {
        if($bytes >= $arItem["VALUE"]) {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}
