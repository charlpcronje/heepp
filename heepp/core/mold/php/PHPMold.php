<?php
namespace core\mold\php;
use core\mold\Mold;

class PHPMold extends Mold {
    public $namespace;
    public $uses;
    
    public function __construct($name,$options = [],$templateParams = []) {
        $defaultTemplateParams = [
            '_start_'       => '<'.'?'.'php ',
            '_end_'         => "\n"
        ];
        
        $defaultOptions = [
            'group'        => 'php',
            'formatOutput' => true,
            'formatter'    => [
                'class'         => '\core\extension\helper\FormatPHP',
                'staticMethod'  => 'format'
            ]
        ];
        $options = array_merge($defaultOptions,$options);
        $templateParams = array_merge($defaultTemplateParams,$templateParams);
        $this->filename = $name.'.php';
        
        parent::__construct($name,$options,$templateParams);
    }
    
    public static function mold($name,$options,$templateParams = []) {
        return (new PHPMold($name,$options,$templateParams))->render();
    }
    
    public function render() {
        parent::render();
        return $this;
    }
}
