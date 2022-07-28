<?php
namespace core\mold\php;

class ClassMold extends PHPMold { 
    public $isAbstract       = false;
    public $class            = null;
    public $extend           = '\core\Heepp';
    public $interfaces       = [];
    public $traits           = [];
    public $classProperties  = [];
    public $methods          = [];
    
    public function __construct($name,$options = [],$templateParams = []) {        
        $this->class = $name;
        $defaultTemplateParams = [
            '_header_'      => [
                '${namespace}',
                '${use}'
            ],
            '_body_'        => [
                '${abstract}class ${class} ${extend}${implements} {',
                '${traits}',
                '${properties}',
                '${methods}'
            ],
            '_footer_'      => '}'
        ];
        $defaultOptions = [
            'type'      => 'class'
        ];
        $options = array_merge($defaultOptions,$options);
        $templateParams = array_merge($defaultTemplateParams,$templateParams);
        parent::__construct($name,$options,$templateParams);
    }
    
    public static function mold($name,$options,$templateParams = []) {
        return (new PHPMold($name,$options,$templateParams))->render();
    }

    // ------------ SETTERS
    public function setIsAbstract($isAbstract = false) {
        $this->isAbstract = $isAbstract;
        return $this;
    }
    
    public function setClass($class) {
        $this->class = $class;
        return $this;
    }
    
    public function addInterface($interface) {
        $this->interfaces[] = $interface;
        return $this;
    }
    
    public function setExtend($extend) {
        $this->extend = $extend;
        return $this;
    }
    
    /*
     * (object) $methodObj
     * $methodObj->name = 'Method Name',
     * $methodObj->params = 'Method arguments',
     * $methodObj->body = 'All the code in the method (method body)
     * $methodObj->scope = 'public, private or protected' (Default = 'public')
     */
    public function addMethod($methodObj) {
        if (!isset($methodObj) || !is_object($methodObj)) {
            return new \Exception('The $methodObj argument must be an instance of \\stdClass');
        }
        if (!isset($methodObj->scope)) {
            $methodObj->scope = 'public';
        }
        $this->methods[$methodObj->name] = $methodObj;
        if (!isset($methodObj->params)) {
            $methodObj->params = [];
        }
        return $this;
    }
    
    public function addMethods($methodObjs) {
        if (!isset($methodObjs) || !is_array($methodObjs)) {
            return new \Exception('The $methodObjs argument must be an instance of an array with items that are an instance of \\stdClass');
        }
        
        foreach($methodObjs as $methodName => $methodObj) {
            if (!isset($methodObj->name)) {
                $methodObj->name = $methodName;
            }
            $this->addMethod($methodObj);
        }
        return $this;
    }
    
    public function addTrait($trait) {
        $this->traits[] = $trait;
        return $this;
    }
    
    public function addProperty($property,$value = 'null',$scope = 'public') {
        $this->classProperties[$property] = new \stdClass();
        if (isset($value)) {
            $this->classProperties[$property]->value = $value;
        }
        $this->classProperties[$property]->scope = $scope;
        return $this;
    }
    
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
        return $this;
    }
    
    public function addUse($use = null) {
        $this->uses[] = $use;
    }

    // ----------- RENDER -----------
    private function renderNamespace() {
        if (isset($this->namespace) && !empty($this->namespace)) {
            $this->moldOutput = str_replace('${namespace}','namespace '.$this->namespace.';',$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${namespace}','',$this->moldOutput);
        }
    }
    
    private function renderUses() {
        if (isset($this->uses) && count($this->uses) > 0) {
            $string = '';
            foreach($this->uses as $use) {
                $string .= 'use '.$use."; \n";
            }
            $this->moldOutput = str_replace('${use}',$string,$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${use}','',$this->moldOutput);
        }
    }

    private function renderIsAbstract() {
        if ($this->isAbstract) {
            $this->moldOutput = str_replace('${abstract}','abstract ',$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${abstract}','',$this->moldOutput);
        }
    }
    
    private function renderClassAndExtend() {
        $this->moldOutput = str_replace('${class}',$this->class,$this->moldOutput);
        if (isset($this->extend) && !empty($this->extend)) {
            $this->moldOutput = str_replace('${extend}','extends '.$this->extend.' ',$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${extend}','',$this->moldOutput);
        }
    }
    
    private function renderInterfaces() {
        if (isset($this->interfaces) && count($this->interfaces) > 0) {
            $string = 'implements ';
            $count = 1;
            foreach($this->interfaces as $interface) {
                $string .= $interface;
                if ($count < count($this->interfaces)) {
                    $string .= ', ';
                }
                $count++;
            }
            $this->moldOutput = str_replace('${implements}',$string,$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${implements}','',$this->moldOutput);
        }
    }
    
    private function renderTraits() {
        if (isset($this->traits) && count($this->traits) > 0) {
            $string = '';
            foreach($this->traits as $trait) {
                $string .= 'use '.$trait."; \n";
            }
            $this->moldOutput = str_replace('${traits}',$string,$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${traits}','',$this->moldOutput);
        }
    }
    
    private function renderProperties() {
        if (isset($this->classProperties) && count($this->classProperties) > 0) {
            $string = '';
            foreach($this->classProperties as $property => $propertySettings) {
                $string .= $propertySettings->scope.' $'.$property = ' '.$propertySettings->value.";\n";
            }
            $this->moldOutput = str_replace('${properties}',$string,$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${properties}','',$this->moldOutput);
        }
    }
    
    private function renderMethods() {
        $template = '
            ${methodScope} function ${methodName}(${methodParams}) {
                ${methodBody}
            }
        ';
        $methodsAdded = 0;
        $string = '';
        if (isset($this->methods) && count($this->methods) > 0) {
            foreach($this->methods as $method) {
                $string .= $template;
                // Add method scope (private, static, public or protected)
                $string = str_replace('${methodScope}',$method->scope, $string);
                // Add Method Name
                $string = str_replace('${methodName}',$method->name, $string);
                // Add Method Arguments
                $params = '';
                $paramsAdded = 1;
                if (isset($method->params) && count($method->params) > 0) {
                    foreach($method->params as $param => $value) {
                        $params .= '$'.$param;
                        if (isset($value)) {
                            $params .= ' = '.$value;
                        }
                        if ($paramsAdded < count($method->params)) {
                            $params .= ',';
                        }
                        $paramsAdded++;
                    }
                    $string = str_replace('${methodParams}',$params, $string);
                } else {
                    $string = str_replace('${methodParams}','', $string);
                }
                // Add Method Body
                $string = str_replace('${methodBody}',$method->body, $string);
            }
            $this->moldOutput = str_replace('${methods}',$string,$this->moldOutput);
        } else {
            $this->moldOutput = str_replace('${methods}','',$this->moldOutput);
        }
    }
    
    public function make($constParam = null) {
        if (!class_exists($this->class)) {
            if (file_exists($this->options['savePath'].$this->filename)) {
                include $this->options['savePath'].$this->filename;
            } else {
                $tempName = uniqid();
                $file = trim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.ltrim($tempName, DIRECTORY_SEPARATOR);
                file_put_contents($file,$this->moldOutput);
                include $file;
                unlink($file);
            }
        }
        if (isset($this->namespace)) {
            $className = '\\'.$this->namespace.'\\'.$this->class;
        } else {
            $className = $this->class;
        }
        if (isset($constParam)) {
            return (new $className($constParam));
        } else {
            return (new $className());
        }
    }
    
    public function render() {
        $this->renderNamespace();
        $this->renderUses();
        $this->renderIsAbstract();
        $this->renderClassAndExtend();
        $this->renderInterfaces();
        $this->renderTraits();
        $this->renderProperties();
        $this->renderMethods();
        parent::render();
        return $this;
    }
}
