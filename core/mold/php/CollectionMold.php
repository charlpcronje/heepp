<?php
namespace core\mold\php;

class CollectionMold extends ClassMold {    
    public function __construct($name,$options = [],$templateParams = []) {
        $defaultOptions = [
            'savePath' => env('project.molds.path').'collections'.DS,
            'formatOutput' => false
        ];
        $this->extend = '\core\extension\database\Model';
        $options = array_merge($defaultOptions,$options);
        parent::__construct($name,$options,$templateParams);
    }
    
    private static function moldMethods($name,$otherProjectMold = '') {
        $otherProjectMold = str_replace('Collection','',$otherProjectMold);
        return [
            '__construct'      => (object)[
                'name' => '__construct',
                'params' => [
                    'name' => 'null'
                ],
                'body'   => '$name = str_replace("Collection","","'.$name.'");
                             parent::__construct("'.$otherProjectMold.'");'
            ]
        ];
    }
    
    public static function mold($name,$options = [],$templateParams = [],$makeAfterMold = true) {
        $otherProjectMold = $name;
        if (strpos($name,'.') !== false) {
            $exp = explode('.',$name);
            //$otherProjectMold = $exp[1];
            $name = array_pop($exp);
        } elseif (strpos($name,'/') !== false) {
            $exp = explode('/',$name);
            //$otherProjectMold = $exp[1];
            $name = array_pop($exp);
        }
        
        $mold = new CollectionMold($name,$options,$templateParams);
        $mold->setIsAbstract(false)
             ->setClass($name)
             ->addMethods(self::moldMethods($name,$otherProjectMold))
             ->render();
        if ($makeAfterMold) {
            return $mold->make($name);
        }
        return $mold->getOutput();
    }
}
