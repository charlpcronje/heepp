<?php

function colorDarken($rgb,$darker=2) {
    if (strpos($darker,'%') !== false) {
        $darker = (int)str_replace('%','',$darker);
        $darker = round($darker / 25,2);
    }
    $hash = (strpos($rgb, '#') !== false) ? '#' : '';
    $rgb = (strlen($rgb) == 7) ? str_replace('#', '', $rgb) : ((strlen($rgb) == 6) ? $rgb : false);
    if(strlen($rgb) != 6) return $hash.'000000';
    $darker = ($darker > 1) ? $darker : 1;
    list($R16,$G16,$B16) = str_split($rgb,2);
    // Added @ to beginnning of lines because of a deprecation in PHP 7.4 on the hexdec function
    @$R = sprintf("%02X", floor(hexdec($R16)/$darker));
    @$G = sprintf("%02X", floor(hexdec($G16)/$darker));
    @$B = sprintf("%02X", floor(hexdec($B16)/$darker));
    return $hash.$R.$G.$B;
}
