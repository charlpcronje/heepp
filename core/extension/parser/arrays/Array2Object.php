<?php
namespace core\extension\parser\arrays;
/**
 * Description of Array2Object
 * Converts an array to a stdClassObject
 * @author Charl
 * @date 2016-04-07
 * @time 05:09 AM
 */
class Array2Object extends \core\Heepp {
    public $results;
    
    function __construct($inputArray) {
        parent::__construct();
        $this->results = $this->parse($inputArray);
    }
    
    function parse($inputArray) {
        $object = new \stdClass();
        foreach ($inputArray as $key => $value) {
            if (is_array($value)) {
                $value = $this->parse($value);
            }
            $object->$key = $value;
        }
        return $object;
    }
}
