<?php
namespace core\extension\parser\XML\FusedObject;
/**
 * Description of Config
 * Settings for FusedObjects, this will set the parser's defaults, import the 
 * ISO Data and set the XML Data Types
 * @author Charl
 */
class Config {
    // Paths
    public $configPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR;
    public $dataPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
    public $dataTypesPath = __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'dataTypes'.DIRECTORY_SEPARATOR;
    
    // Parser Properties
    private $XMLSettings;
    public $results;
    
    function __construct($resetConfigInstance = false) {
        if ($resetConfigInstance) {
            XMLInstances::resetConfigInstance();
        }
        
        $this->results = XMLInstances::getConfigInstance();
        $this->setResultsObjects();
        $this->loadSettings();
        $this->loadData();
        $this->loadDataTypes();
    }
    
    private function loadDataTypes() {
        foreach($this->XMLSettings->dataTypes->import as $import) {
            $dataTypeElements = (new \core\extension\parser\XML\XML2Array($this->dataTypesPath.$import->attributes->src,'root',true,'stdClass'))->results;
            foreach($dataTypeElements as $dataTypeKey => $dataTypeElement) {
                $this->results->dataTypes->{$dataTypeKey} = new \stdClass ();
                if (isset($dataTypeElement->attributes)) {
                    
                    // Check if one type should extend another, the defaultValue and 
                    // alias are excluded from extending
                    if (isset($dataTypeElement->attributes->extends)) {
                        $this->results->dataTypes->{$dataTypeKey}->extends = $dataTypeElement->attributes->extends;
                        // Add the base type's restrictions
                        if (isset($this->results->dataTypes->{$dataTypeElement->attributes->extends})) {
                            if (isset($this->results->dataTypes->{$dataTypeElement->attributes->extends}->restrictions)) {
                                $this->results->dataTypes->{$dataTypeKey}->restrictions = $this->results->dataTypes->{$dataTypeElement->attributes->extends}->restrictions;
                            }
                        }
                    }
                    
                    // Set the TypeDefault
                    if (isset($dataTypeElement->attributes->defaultValue)) {
                        $this->results->dataTypes->{$dataTypeKey}->defaultValue = $dataTypeElement->attributes->defaultValue;
                    }
                    
                    // Set the TypeRestrictions
                    if (isset($dataTypeElement->attributes->restrictions)) {
                        if (isset($this->results->dataTypes->{$dataTypeKey}->restrictions)) {
                            $this->results->dataTypes->{$dataTypeKey}->restrictions .= ','.$dataTypeElement->attributes->restrictions;
                        } else {
                            $this->results->dataTypes->{$dataTypeKey}->restrictions = $dataTypeElement->attributes->restrictions;
                        }
                    }

                    // This must always be specified last becuase the aliases needs to get the entire get the entire element stdClass
                    if (isset($dataTypeElement->attributes->alias)) {
                        $aliases = explode(',',$dataTypeElement->attributes->alias);
                        foreach($aliases as $alias) {
                            $this->results->dataTypes->{$alias} = $this->results->dataTypes->{$dataTypeKey};
                            // The alias automatically extends it's parent the same as it's parent
                            if (!isset($this->results->dataTypes->{$alias}->extends)) {
                                $this->results->dataTypes->{$alias}->extends = $dataTypeKey;
                            }
                        }
                    }
                }
                
            }
        }
    }
    
    private function loadData() {
        foreach($this->XMLSettings->data->import as $import) {
            // Load the data from XML File imported, the data will be assigned to the $dataElements variable
            $dataElements = (new \core\extension\parser\XML\XML2Array($this->dataPath.$import->attributes->src,'root',true,'array'))->results;
            
            // Iterate trough the dataElements and extract the data's key value pair as specified in the settings with the key value attributes
            foreach($dataElements as $dataKey => $dataValue) {
                /*
                 * Check if the "key" or "value" should be the value of the $dataKey(nodeName).
                 * So (nodeName) is a special key word to refer to the node name
                 */
                if ($import->attributes->key == 'nodeName') {
                    $key = $dataKey;
                }
                if ($import->attributes->value == 'nodeName') {
                    $value = $dataKey;
                } else {
                    //$dataObject = (new \core\extension\parser\arrays\Array2Object($dataValue))->results;
                    $value = $dataValue;
                    foreach(explode('.',$import->attributes->value) as $arrayKey) {
                        $value = $value[$arrayKey];
                    }
                }
                
                $this->results->data->{$import->attributes->name}[$key] = $value;
            }
        }
    }
    
    private function setResultsObjects() {
        if (!isset($this->results->settings)) {
            $this->results->settings = new \stdClass();
            $this->results->data = new \stdClass();
            $this->results->dataTypes = new \stdClass();
        }
    }
    
    private function loadSettings() {
        $this->XMLSettings = (new \core\extension\parser\XML\XML2Array($this->configPath.'settings.xml','settings'))->results;
    }
            
    public function getResults() {
        return $this->results;
    }
}
