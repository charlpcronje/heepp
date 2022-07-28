<?php
namespace core\element\base;
use core\extension\ui\view;
use core\Element;

class jsonEncode extends Element {
    /* BitMask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
       JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT,
       JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR.
       The behaviour of these constants is described on the JSON constants page.

        http://php.net/manual/en/function.json-encode.php
    */
    public $options = 'JSON_PRETTY_PRINT';

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        return json_encode(trim($this->html),JSON_PRETTY_PRINT);
    }
}
