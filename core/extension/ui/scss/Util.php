<?php
namespace core\extension\ui\scss;
use core\extension\ui\scss\base\Range;
use core\extension\ui\scss\exception\RangeException;

class Util {
    public static function checkRange($name, Range $range, $value, $unit = '') {
        $val = $value[1];
        $grace = new Range(-0.00001, 0.00001);
        if ($range->includes($val)) {
            return $val;
        }
        if ($grace->includes($val - $range->first)) {
            return $range->first;
        }
        if ($grace->includes($val - $range->last)) {
            return $range->last;
        }
        throw new RangeException("$name {$val} must be between {$range->first} and {$range->last}$unit");
    }

    public static function encodeURIComponent($string) {
        $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
        return strtr(rawurlencode($string), $revert);
    }
}
