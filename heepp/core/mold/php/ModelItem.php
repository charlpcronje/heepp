<?php
namespace core\mold\php;

class ModelItem extends ClassMold {
    
    public function __construct($name,$options = [],$templateParams = []) {
        $defaultOptions = [
            'savePath' => env('project.molds.path').'objects'.DS,
            'formatOutput' => false
        ];
        $this->extend = null;
        $options = array_merge($defaultOptions,$options);
        parent::__construct($name,$options,$templateParams);
    }
    
    private static function moldMethods() {
        return [
            'getIterator' => (object)[
                'name' => 'getIterator',
                'body' => 'return new \ArrayIterator($this);'
            ],
            '__get'      => (object)[
                'name' => '__get',
                'params' => [
                    'property' => null
                ],
                'body'   => 'return $this->$property;'
            ],
            '__set'      => (object)[
                'params' => [
                    'property' => null,
                    'value'    => 'null'
                ],
                'body'   => '$this->$property = $value;'
            ]
        ];
    }
    
    public static function mold($name,$options = [],$templateParams = [],$makeAfterMold = true) {
        $mold = new ModelItem($name,$options,$templateParams);
        $mold->setIsAbstract(false)
             ->addInterface('\IteratorAggregate')
             ->setClass($name)
             ->addMethods(self::moldMethods())
             ->render();
        if ($makeAfterMold) {
            return $mold->make();
        }
        return $mold->getOutput();
    }
}
