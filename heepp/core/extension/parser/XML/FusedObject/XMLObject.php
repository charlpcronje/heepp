<?php
namespace core\extension\parser\XML\FusedObject;
use core\extension\Extension;

class XMLObject extends Extension implements \IteratorAggregate, \Countable  {
    public $object;

    // Config Properties
    private $config;
    
    // Settings Properties
    private $XMLObject = null;
    public $resetObjectInstance = true;
    public $resetLookupInstance = true;
    public $isLookup = false;
    
    // Parser Properties
    private $XMLReader;
    private $path;
    private $pathVerified = false;
    
    // Output Properties
    private $construct;
    
    private $constructAttributes = array();
    
    private $objectSet;
    private $objectSetParams = array();
    private $objectModelDataSet;
    private $objectModelParams = array();
    public  $objectData;
    
    private $model = null;

    function __construct($XMLObject = null,$resetObjectInstance = true,$isLookup = false,$resetLookupInstance = true) {
        //$config = (new Config())->results;
        
        if ($resetObjectInstance) {
            XMLInstances::resetObjectInstance();
        }
        if ($resetLookupInstance) {
            XMLInstances::resetLookupInstance();
        }
        
        if ($isLookup) {
            $this->object = XMLInstances::getLookupInstance();
        } else {
            $this->object = XMLInstances::getObjectInstance();
        }

        //$this->config = $config;
        $this->XMLReader = new \core\extension\parser\XML\Sabre\Reader();
        
        if (isset($XMLObject)) {
            $this->XMLObject = $XMLObject;
            $this->buildObjectPath();
            if (file_exists($this->path)) {
                $this->getXMLObjectContents();
                $this->loadObjectsToExtend();
                $this->loadXMLConstruct();
                $this->loadObjectModel();
                $this->loadObjectProperties();
                
                //$this->object->construct = $this->construct;
                //$this->object->properties = $this->object->properties;
                //$this->object->data = $this->objectData;
//                $ref = new \ReflectionClass('dialog');
//                foreach ($ref->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
//                    if (isset($this->{$property->name})) {
//                        $this->dialog->{$this->id}->{$property->name} = $this->{$property->name};
//                    }
//                }
                return $this->object;
            } else {
                throw new \Exception("Object: '" . $XMLObject . "' could not be found.");
            }
        } else {
            if (!isset($XMLObject)) {
                throw new \Exception("No object specified");
            }
        }
    }
    
    function loadModelData($keyValue = null) {
        if (isset($this->object->construct->model)) {
            print_r($this->object->construct);
            
            $this->object->construct->model->loadDataSet($this->objectModelDataSet);            // Load Dataset specified in XML
            if (isset($keyValue)) {
                $this->object->construct->model->{$this->object->construct->key}($keyValue);
            }
            $this->object->construct->model->expressionParams = $this->objectModelParams;       // Make the model expressionParams equal to the objectModelParams
            $this->object->construct->model->runDataSet();                                      // Now run the dataset and get the data
        }
        return $this->setObjectData($this->object->construct->model->results);
    }
    
    private function loadLookup($lookupObject) {
        $lookup = new XMLObject($lookupObject['object']);
        foreach(explode(',',$lookupObject['properties']) as $property) {
            print_r($this->object->construct->name);
            $prefixes = array(
                'name'=>str_replace('.','_',$this->object->construct->name).'_',
                'alias'=>$this->object->construct->alias.' ',
            );
            
            if (!isset($this->object->properties->$property)) {
                $this->object->properties->$property = new \stdClass();
            }
            foreach((array)$lookup->object->properties->$property as $lookupProperty => $lookupPropertyValue) {
                if (isset($prefixes[$lookupProperty])) {
                    $lookupPropertyValue = $prefixes[$lookupProperty].$lookupPropertyValue;
                }
                $this->object->properties->$property->$lookupProperty = $lookupPropertyValue;
            }
            
        }
        $this->loadModelData($lookup->object->construct->model);
    }
    
    private function loadPropertyValues($values) {
        foreach($values as $value) {
            switch(strtolower(str_replace('{}','', $value['name']))) {
                case 'lookup':
                    return $this->loadLookup($value['attributes']);
            }
        }
        
    }
    
    protected function loadObjectProperties() {
        if (!isset($this->object->properties)) {
            $this->object->properties = new \stdClass();
        }
        foreach($this->XMLReader->parseInnerTree() as $property) {
            if (is_array($property['value'])) {
                $this->loadPropertyValues($property['value']);
            }
            switch(strtolower(str_replace('{}','',$property['name']))) {
                case 'property':
                    if (!isset($this->object->properties->{$property['attributes']['name']})) {
                        $this->object->properties->{$property['attributes']['name']} = new \stdClass();
                    }
                    foreach($property['attributes'] as $attrKey => $attr) {
                        $this->object->properties->{$property['attributes']['name']}->$attrKey = $attr;
                    }
                break;
                case 'object':
                    
                break;
            }
        }
    }
    
    protected function loadObjectModel() {
        if (isset($this->object->construct->model) && is_string($this->object->construct->model)) {
            $this->object->construct->model = new \core\extension\database\Model($this->object->construct->model);
        }
    }
    
    private function loadObjectsToExtend() {
        $this->XMLReader->next();
        $this->constructAttributes = $this->XMLReader->parseAttributes();
        if (isset($this->constructAttributes['extends'])) {
            foreach(explode(',',$this->constructAttributes['extends']) as $extend) {
                new XMLObject($extend,false);
            }
        }
    }
    
    private function loadXMLConstruct() {
        if (!isset($this->object->construct)) {
            $this->object->construct = new \stdClass();
        }
        
        foreach($this->constructAttributes as $attribute => $attributeValue) {
            $this->object->construct->{$attribute} = $attributeValue;
        }
    }
    
    private function getXMLObjectContents() {
        $this->XMLReader->XML(file_get_contents($this->path));
    }

    private function checkPath($path, $nextPath) {
        $this->pathVerified = false;
        if (file_exists($path . '.xml')) {
            $this->pathVerified = true;
            if (is_dir($path)) {
                if (file_exists($path . DIRECTORY_SEPARATOR . $nextPath . '.xml')) {
                    $this->pathVerified = false;
                }
            }
        }
        return $this->pathVerified;
    }

    private function buildObjectPath() {
        // Start Building Path
        $path = str_replace('/', '', env('project.objects.path'));
        $nextPath = env('project.objects.path');
        $objSplits = explode('.', $this->XMLObject);
        $i = 0;
        foreach ($objSplits as $split) {
            $path .= DS . $split;
            $nextPath = null;
            if (isset($objSplits[$i + 1])) {
                $nextPath = $objSplits[$i + 1];
            }

            if ($this->checkPath($path, $nextPath)) {
                $this->path = $path . '.xml';
                $this->objectSet = $nextPath;
                for ($e = $i + 1,$eMax = count($objSplits); $e < $eMax; $e++) {
                    $this->objectSetParams[] = $objSplits[$e];
                }
            }
            $i++;
        }
    }

    public function __call($name, $arguments) {
        if (isset($arguments[0])) {
            $this->objectModelParams[$name] = $arguments[0];
        }
        return $this;
    }
    
    function __set($name, $value) {
        parent::__set($name, $value);
    }

    function setObjectData(Traversable $modelData) {
        foreach ($this->object->properties as $property) {
            $this->object->data->{$property->name} = $modelData[0][$property->name];
        }
        return $this->object->data;
    }

    public function __toString() {
        return (string)json_encode($this->object);
    }

    public function __toArray() {
        return (array) $this->object;
    }
    
    public function count() {
        return count($this->object->data);
    }
    
    public function getIterator() {
        return new \ArrayIterator($this);
    }

//    public function __toObject() {
//        return $this->object;
//    }

}
