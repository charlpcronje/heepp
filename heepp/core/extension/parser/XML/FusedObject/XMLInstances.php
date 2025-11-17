<?php
namespace core\extension\parser\XML\FusedObject;
/**
 * Description of XMLInstances
 * Instances of objects being parsed and fused
 * @author Charl
 * @date 2016-04-05
 * @time 17:50
 */
class XMLInstances {
    static $configInstance;
    static $objectInstance;
    static $lookupInstance;
    
    public $construct;
    
    public static function resetObjectInstance() {
        static::$objectInstance = null;
    }
    
    public static function resetLookupInstance() {
        static::$lookupInstance = null;
    }
    
    public static function getConfigInstance() {
        if (!isset(static::$configInstance)) {
            static::$configInstance = new static;
        }
        return static::$configInstance;
    }
    
    public static function getLookupInstance() {
        if (!isset(static::$objectInstance)) {
            static::$objectInstance = new static;
            static::$objectInstance->construct = new \stdClass();
            static::$objectInstance->properties = new \stdClass();
            static::$objectInstance->data = new \stdClass();
        }
        return static::$objectInstance;
    }
    
    public static function getObjectInstance() {
        if (!isset(static::$objectInstance)) {
            static::$objectInstance = new static;
            static::$objectInstance->construct = new \stdClass();
            static::$objectInstance->properties = new \stdClass();
            static::$objectInstance->data = new \stdClass();
        }
        return static::$objectInstance;
    }
}
