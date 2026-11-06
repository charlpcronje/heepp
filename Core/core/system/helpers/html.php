<?php

function removeCSSAndScripts($html) {
    $html = preg_replace('/<style[^>]*>.*<\/style[^>]*>/i','',$html);
    $html = preg_replace('/<script[^>]*>.*(<\/script[^>]*>|$)/i','',$html);
    return $html;
}

function removeCSS($html) {
    return preg_replace('/<style[^>]*>.*<\/style[^>]*>/i','',$html);
}

/**
 * @deprecated Use HTMLPurifier
 */
function removeScripts($html) {
    if (is_array($html)) {
        foreach ($html as $k => &$v) {
            $v = remove_scripts($v);
        }
        return $html;
    }
    return preg_replace('/<script[^>]*>.*(<\/script[^>]*>|$)/i','',$html);
}

function removeImagesFromHTML($html) {
    $html = preg_replace('/background="[^"]*"/i','',$html);
    $html = preg_replace('/background-image:url\([^\)]*\)/i','',$html);

    $html = preg_replace('/<img[^>]*>/i', '', $html);
    return preg_replace('/<\/img>/i', '', $html);
}

function htmlHasImages($html) {
    return preg_match('/<img[^>]*>/i',$html) > 0 || preg_match('/background-image:url\([^\)]*\)/i', $html) > 0 || preg_match('/background="[^"]*"/i',$html) > 0;
}

function convertToLinks($text) {
    $orig = $text;
    //Replace full urls with hyperinks. Avoids " character for already rendered hyperlinks
    $text = preg_replace('@([^"\']|^)(https?://([-\w\.]+)+(:\d+)?(/([%\w/_\-\.\:\#\+]*(\?[^\s<]+)?)?)?)@', '$1<a href="$2" target="_blank">$2</a>', $text);

    //Convert every word starting with "www." into a hyperlink
    $text = preg_replace('@(>|\s|^)(www.([-\w\.]+)+(:\d+)?(/([%\w/_\-\.\:\#\+]*(\?[^\s<]+)?)?)?)@', '$1<a href="http://$2" target="_blank">$2</a>', $text);

    //Convert every email address into an <a href="mailto:... hyperlink
    $text = preg_replace('/([^\:a-zA-Z0-9>"\._\-\+=])([a-zA-Z0-9]+[a-zA-Z0-9\._\-\+]*@[a-zA-Z0-9_\-]+([a-zA-Z0-9\._\-]+)+)/', '$1<a href="mailto:$2" target="_blank">$2</a>', $text);
    Hook::fire('convert_to_links', array('original' => $orig, 'text' => $text), $text);
    return $text;
}

function parseTagsAndAttributes($element,$attribute_array = 0) {
    // If second argument is not received, it means a closing tag is being handled
    if(is_numeric($attribute_array)){
        return "</$element>";
    }

    static $id = 0;
    // Remove any duplicate element
    // if($element == 'param' && isset($attribute_array['allowscriptaccess'])){
    //    return '';
    // }

    $new_element = '';
    // Force a serialized ID number
    // $attribute_array['id'] = 'my_'. $id;
    // ++$id;

    // Inject param for allowScriptaccess
    //if($element == 'object') {
    //    $new_element = '<param id="my_'. $id.'" allowscriptaccess="never" />';
    //    ++$id;
    //}

    $string = '';
    foreach($attribute_array as $k=>$v){
        $string .= " {$k}=\"{$v}\"";
    }

    static $empty_elements = ['area'=>1,'br'=>1,'col'=>1,'command'=>1,'embed'=>1,'hr'=>1,'img'=>1,'input'=>1,'isindex'=>1,'keygen'=>1,'link'=>1,'meta'=>1,'param'=>1,'source'=>1,'track'=>1,'wbr'=>1];
    return "<{$element}{$string}".(array_key_exists($element,$empty_elements)?'/':''). '>'. $new_element;
}