<?php
namespace core\extension\parser\XML\FusedObject;
/**
 * @author Charl Cronje
 * @date 2016-05-04
 * @time 11:17:00 AM
 */
class XMLObject extends \core\extension\Extension {
    // Config Properties
    private $config;
    
    // Settings Properties
    public $XMLObject = null;
    public $resetObjectInstance = true;
    public $resetLookupInstance = true;
    public $isLookup = false;
    
    // Parser Properties
    private $XMLReader;
    public $path;
    public $pathVerified = false;
    public $objectConstruct;
    
    // Output Properties
    public $result;                             // stdClass Object that will contain the parsed result
    public $arrayObject;
    
    private $xml = null;
    
    private $constructAttributes = array();
    private $objectProperties = array();
    
    public $objectSet;
    public $objectSetParams = array();
    public $objectKey = array();
    public $objectModel;
    public $objectModelDataSet;
    public $objectModelParams = array();
    
    private $XMLProperties = null;
    public $objProperties = array();
    public $model = null;

    function __construct($XMLObject = null,$resetObjectInstance = true,$isLookup = false,$resetLookupInstance = true) {
        $config = (new Config())->results;
        
        if ($resetObjectInstance) {
            XMLInstances::resetObjectInstance();
        }
        if ($resetLookupInstance) {
            XMLInstances::resetLookupInstance();
        }
        
        if ($isLookup) {
            $this->results = XMLInstances::getLookupInstance();
        } else {
            $this->results = XMLInstances::getObjectInstance();
        }
        
        $this->results->config = $config;
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
                
                $this->loadXMLProperties();
                //$this->parseXMLProperties();
                //$this->buildPHPObject();
                return $this->results;
            } else {
                throw new \Exception("Object: '" . $XMLObject . "' could not be found.");
            }
        } else {
            if (!isset($XMLObject)) {
                throw new \Exception("No object specified");
            }
        }
    }
    
    public function parse() {
        
    }
    
    function loadModelData() {
        $this->model = new Model($this->objectModel);                 // Load database model as specified in the object XML
        $this->model->loadDataSet($this->objectModelDataSet);         // Load Dataset specified in XML
        $this->model->expressionParams = $this->objectModelParams;    // Make the model expressionParams equal to the objectModelParams
        $modelData = $this->model->runDataSet()->results;             // Now run the dataset and get the data
        return $this->setObjectData($modelData);
    }
    
    private function loadLookup($lookupObject) {
        $lookup = new XMLObject($lookupObject['object']);
        
        foreach(explode(',',$lookupObject['properties']) as $property) {
            $prefixes = array(
                'name'=>  str_replace('.','_',$this->results->construct->name).'_',
                'alias'=>$this->results->construct->alias.' ',
            );
            
            if (!isset($this->results->$property)) {
                $this->results->$property = new \stdClass();
            }
            foreach((array)$lookup->results->$property as $lookupProperty => $lookupPropertyValue) {
                if (isset($prefixes[$lookupProperty])) {
                    $lookupPropertyValue = $prefixes[$lookupProperty].$lookupPropertyValue;
                }
                $this->results->$property->$lookupProperty = $lookupPropertyValue;
            }
        }

        //$this->loadModelData($lookup->results->construct->model);
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
        $this->objectProperties = $this->XMLReader->parseInnerTree();
        foreach($this->objectProperties as $property) {
            if (is_array($property['value'])) {
                $this->loadPropertyValues($property['value']);
            }
            switch(strtolower(str_replace('{}','', $property['name']))) {
                case 'property':
                    if (!isset($this->results->{$property['attributes']['name']})) {
                        $this->results->{$property['attributes']['name']} = new \stdClass();
                    }
                    foreach($property['attributes'] as $attrKey => $attr) {
                        $this->results->{$property['attributes']['name']}->$attrKey = $attr;
                    }
                break;
                case 'object':
                    
                break;
            }
        }
    }
    
    protected function loadObjectModel() {
        if (isset($this->results->construct->model) && is_string($this->results->construct->model)) {
            //$this->results->construct->model = new \core\extension\database\Model($this->results->construct->model);
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
        foreach($this->constructAttributes as $attribute => $attributeValue) {
            $this->results->construct->{$attribute} = $attributeValue;
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
            $path .= DIRECTORY_SEPARATOR . $split;
            if (isset($objSplits[$i + 1])) {
                $nextPath = $objSplits[$i + 1];
            } else {
                $nextPath = null;
            }

            if ($this->checkPath($path, $nextPath)) {
                $this->path = $path . '.xml';
                $this->objectSet = $nextPath;
                for ($e = $i + 1; $e < count($objSplits); $e++) {
                    $this->objectSetParams[] = $objSplits[$e];
                }
            }
            $i++;
        }
    }

    private function loadXMLProperties() {
        if (!empty($this->XMLProperties = (string) $this->xml->{$this->objectSet}['key'])) {
            $this->objectKey = explode(',', (string) $this->xml->{$this->objectSet}['key']);
        }
        if (!empty($this->XMLProperties = $this->xml->{$this->objectSet}['model'])) {
            $this->objectModel = (string) $this->xml->{$this->objectSet}['model'];
        }
        if (!empty($this->XMLProperties = $this->xml->{$this->objectSet}['model-dataset'])) {
            $this->objectModelDataSet = (string) $this->xml->{$this->objectSet}['model-dataset'];
        }

        $this->XMLProperties = $this->xml->{$this->objectSet}->children();
    }

    private function parseXMLProperties() {
        //$XMLProperties = clone $this->XMLProperties;
        if (@count($this->XMLProperties) > 0) {
            foreach ($this->XMLProperties as $xmlProperty) {
                $property = new \stdClass();

                if (!empty((string) $xmlProperty['name'])) {
                    $property->name = (string) $xmlProperty['name'];
                }

                if (!empty((string) $xmlProperty['type'])) {
                    $property->type = (string) $xmlProperty['type'];
                }

                if (!empty((string) $xmlProperty['default'])) {
                    $property->default = (string) $xmlProperty['default'];
                } else {
                    $property->default = "";
                }

                if (!empty((string) $xmlProperty['value'])) {
                    $property->value = (string) $xmlProperty['value'];
                }

                if (!empty((string) $xmlProperty['src'])) {
                    $property->src = (string) $xmlProperty['src'];
                }

                if (!empty((string) $xmlProperty['format'])) {
                    $property->format = (string) $xmlProperty['format'];
                }
                $this->objProperties[] = $property;
            }
        }
    }

    private function buildPHPObject() {
        $this->object = new \stdClass();
        foreach ($this->objProperties as $property) {
            // Cast the property to it's type
            switch (strtolower($property->type)) {
                case 'int':
                    if (isset($property->default)) {
                        $this->object->{(string) $property->name} = (int) $property->default;
                    } else {
                        $this->object->{(string) $property->name} = null;
                    }
                    break;
                case 'string':
                    if (isset($property->default)) {
                        $this->object->{(string) $property->name} = (string) $property->default;
                    } else {
                        $this->object->{(string) $property->name} = null;
                    }
                    break;
                case 'decimal':
                    if (isset($property->default)) {
                        $this->object->{(string) $property->name} = (float) $property->default;
                    } else {
                        $this->object->{(string) $property->name} = null;
                    }
                    break;
                case 'date':
                    if (isset($property->default)) {
                        $this->object->{(string) $property->name} = (string) $property->default;
                    } else {
                        $this->object->{(string) $property->name} = null;
                    }
                    break;
                case 'datetime':
                    if (isset($property->default)) {
                        switch ((string) $property->default) {
                            case 'timestamp':
                                $this->object->{(string) $property->name} = date('Y-m-d H:i:s');
                                break;
                            default:
                                $this->object->{(string) $property->name} = (string) $property->default;
                                break;
                        }
                    } else {
                        $this->object->{(string) $property->name} = null;
                    }
                    break;
                case 'object':
                    //$this->object->{$property->name} = new XMLObject($property->src);
                    $this->object->{(string) $property->name} = new XMLObject((string) $property->src);
                    break;
            }

            if (!empty($property->format)) {
                $formatSplit = explode('/', (string) $property->format);
                switch ($formatSplit[0]) {
                    case 'round':
                        $this->object->{$property->name} = round((int) $this->object->{$property->name}, $formatSplit[1]);
                        break;
                    case 'date':
                        $this->object->{$property->name} = date('Y-m-d', strtotime((string) $this->object->{$property->name}));
                        break;
                    case 'datetime':
                        $this->object->{$property->name} = date('Y-m-d H:i:s', strtotime((string) $this->object->{$property->name}));
                        break;
                    case 'telephone':
                        $this->object->{$property->name} = '(' . substr((string) $this->object->{$property->name}, 0, 3) . ') ' . substr((string) $this->object->{$property->name}, 3, 3) . '-' . substr((string) $this->object->{$property->name}, 6);
                        break;
                    case 'array':
                        $this->object->{$property->name} = date('Y-m-d', strtotime($this->object->{$property->name}));
                        break;
                }
            }
        }
        return $this->object;
    }

    public function __call($name, $arguments) {
        if (isset($arguments[0])) {
            $this->objectModelParams[$name] = $arguments[0];
        }
        return $this;
    }

    function setObjectData($modelData) {
        $this->arrayObject = array();
        foreach ($modelData as $dataRow) {
            $this->arrayObject[$dataRow[$this->objectKey[0]]] = array();
            foreach ($this->objProperties as $property) {
                $this->arrayObject[$dataRow[$this->objectKey[0]]][$property->name] = $dataRow[$property->name];
            }
        }
        foreach ($this->objProperties as $property) {
            $this->object->{$property->name} = $modelData[0][$property->name];
        }
        return $this->arrayObject;
    }

    public function __toString() {
        return json_encode($this->object);
    }

    public function __toArray() {
        return (array) $this->object;
    }

//    public function __toObject() {
//        return $this->object;
//    }

}
