<?php
namespace core\extension\database;

class MoldedModel implements \Iterator, \Countable {
    protected $modelObject;
    
    private $model;
    private $collection   = [];
    private $currentIndex = 0;
    private $keys         = null;
    
    public $properties;
    public $modified      = [];
    public $affectedRows  = 0;
    public $lastInsertId  = 0;
   
    function __construct($modelObject = null) {
        $this->properties = (object)[];
        if (isset($modelObject) && $modelObject instanceof \core\extension\database\Model) {
            // Setting this->modelObject = $modelObject
            $this->setModelObject($modelObject);
            
            // Creating a friendlier view of the model in modelObject
            if (isset($this->modelObject)) {
                $this->setModelDetails();
                // Set $this->properties definition (Not the values yet) from $this->model
                $this->setModelColumnsAsProperties();
            }
        }        
    }
    
    public function __debugInfo() {
        $return = [
            'collection' => $this->collection,
            'properties' => $this->properties,
            'key'        => $this->model->definition->key,
            'sqlQuery'   => $this->model->logs->sqlQuery
        ];
        if (count($this->modified) > 0) {
            $return['modified'] = $this->modified;
        }
        
        if ($this->affectedRows > 0) {
            $return['affectedRows'] = $this->affectedRows;
        }
        
        if ($this->lastInsertId > 0) {
            $return['lastInsertId'] = $this->lastInsertId;
        }
        return $return;
    }
    
    public function add($item, $key = null){
        // Check if the current object has the property the item is trying to set
        foreach((object)$item as $property => $value) {
            if (!property_exists($this->properties,$property)) {
                unset($item->$property);
            }
        }
        
        if ($key === null){
            $this->collection[] = $item;
        } else {
            // key was specified, check if key exists
            if (isset($this->collection[$key])) {
                new \Exception('Trying to add duplicate key: "'.$key.'" to "'.$this->model->definition->model.'" collection');
            } else {
                $this->collection[$key] = $item;
            }
        }
    }
    
    public function run() {
        $this->modelObject->runDataSet();
        $count = 0;
        foreach($this->modelObject->getResults() as $result) {
            if ($count == 0) {
                foreach($result as $key => $value) {
                    $this->properties->$key = $value;
                }
            }
            $this->add($result);
            $count++;
        }
        $this->setModelDetails();
        return $this;
    }
    
    public function __set($property, $value = null) {
        if (isset($this->model->definition->columns->{$property})) {
            if ($this->properties->{$property} != $value) {
                if (isset($this->modified[$property])) {
                    $this->modified[$property] = (object)[
                        'from' => $this->modified[$property]->from,
                        'to'   => $value
                    ]; 
                } else {
                    $this->modified[$property] = (object)[
                        'from' => $this->properties->{$property},
                        'to'   => $value
                    ];
                }
                $this->properties->{$property} = $value;
            }
        }
        return $this;
    }
    
    public function save() {
        if (isset($this->properties->{$this->model->definition->key}) && !empty($this->properties->{$this->model->definition->key})) {
            $this->update((array)$this->properties);
        }
    }
    
    public function __get($name) {
        if (isset($this->properties->{$name})) {
            return $this->properties->{$name};
        } else {
            return null;
        }
    }
    
    public function __call($name, $arguments) {
        if (isset($this->model->datasets->$name)) {
            $this->modelObject->loadDataSet($name);
            if (isset($arguments) && is_array($arguments) && count($arguments) > 0) {
                foreach($arguments[0] as $argumentName => $argumentValue) {
                    $this->modelObject->setexpressionParam($argumentName,$argumentValue);
                }
            }
        } elseif (isset($arguments) && count($arguments) > 0) {
            $this->modelObject->setexpressionParam($name,$arguments[0]);
        }
        $this->setModelDetails();
        return $this;
    }
    
    public function find($key) {
        $this->clear();
        $record = (object)$this->modelObject->getRecord($key);
        
        if ($this->modelObject->getGotResults()) {
            $this->add($record);
            $this->next();
            $this->setObjectToCurent();
        }
        return $this;
    }
    
    private function setObjectToCurent() {
        $current = $this->current();
        foreach($current as $property => $value) {
            $this->setProperty($property,$value);
        }
    }
    
    public function setProperty($property,$value) {
        if (property_exists($this->properties,$property)) {
            if ($this->properties->{$property} != $value) {
                if (isset($this->modified[$property])) {
                    $this->modified[$property] = (object)[
                        'from' => $this->modified[$property]->from,
                        'to'   => $value
                    ]; 
                } else {
                    $this->modified[$property] = (object)[
                        'from' => $this->properties->{$property},
                        'to'   => $value
                    ];
                }
                $this->properties->{$property} = $value;
            }
        }
    }
    
    public function current() {
        return $this->collection->get($this->key());
    }
    
    public function key() {
        return $this->keys[$this->currentIndex];
    }
    
    public function next() {
        ++$this->currentIndex;
    }
    
    public function valid() {
        return isset($this->keys[$this->currentIndex]);
    }

    public function rewind() {
        $this->currentIndex = 0;
    }

    // ----------- IMPLEMENTATIONS
   // public function getIterator() {
    //    return new collectionIterator($this);
//        return (function () {
//            while(list($key, $val) = each($this->collection)) {
//                $this->key = $val;
//                yield $key => $val;
//            }
//        })();
   // }
    
    public function count() {
        return $this->count($this->collection);
    }
    
    public function __sleep() {
        $this->model->definition->xml = json_encode($this->model->definition->xml);
        return [
            'properties',
            'collection',
            'modified',
            'affectedRows',
            'lastInsertId'
        ];
    }
    
    
    // ----------- SETTERS
    private function setModelObject($modelObject) {
        $this->modelObject = $modelObject;
    }
    
    private function setModelDetails() {
        $this->model = new \stdClass();
        // Model Definition
        $this->model->definition = new \stdClass();
        $this->model->definition->model       = $this->modelObject->getModel();
        $this->model->definition->modelAlias  = $this->modelObject->getModelAlias();
        $this->model->definition->table       = $this->modelObject->getTable();
        $this->model->definition->key         = $this->modelObject->getKey();
        $this->model->definition->columns     = $this->modelObject->getColumns();
        $this->model->definition->path        = $this->modelObject->getPath();
        $this->model->definition->combKey     = $this->modelObject->getCombKey();
        $this->model->definition->constants   = $this->modelObject->getConstants();
        $this->model->definition->xml         = $this->modelObject->getXml();
        $this->model->definition->xmlArray    = json_decode(json_encode($this->model->definition->xml));
        
        // Model Settings
        $this->model->settings = new \stdClass();
        //$this->model->settings->connection     = $this->modelObject->getConnection();
        $this->model->settings->saveLog        = $this->modelObject->getSaveLog();
        $this->model->settings->saveSelectLog  = $this->modelObject->getSaveSelectLog();
        $this->model->settings->useDefaults    = $this->modelObject->getUseDefaults();
        $this->model->settings->ignoreTriggers = $this->modelObject->getIgnoreTriggers();
        $this->model->settings->return         = $this->modelObject->getReturn();
        
        // Model Validation
        $this->model->validation = new \stdClass();
        $this->model->validation->validateValues = $this->modelObject->getValidateValues();
        $this->model->validation->validations    = $this->modelObject->getValidations();
        $this->model->validation->requirements   = $this->modelObject->getRequirements();
        $this->model->validation->checkRequired  = $this->modelObject->getCheckRequired();
        $this->model->validation->defaults       = $this->modelObject->getDefaults();
        
        // Model Select
        $this->model->select = new \stdClass();
        $this->model->select->columns            = $this->modelObject->getColumns();
        $this->model->select->joins              = $this->modelObject->getJoins();
        $this->model->select->filter             = $this->modelObject->getFilter();
        $this->model->select->group              = $this->modelObject->getGroup();
        $this->model->select->order              = $this->modelObject->getOrder();
        $this->model->select->limit              = $this->modelObject->getLimit();
        
        // Model Datasets
        $this->model->datasets = new \stdClass();
        if (isset($this->model->definition->xmlArray->datasets)) {
            foreach($this->model->definition->xml->datasets->children() as $dataset) {
                $datasetName = $dataset->getName();
                $dataSetAttributes = $dataset->attributes();
                $this->model->datasets->{$datasetName} = new \stdClass();
                
                if (isset($dataSetAttributes['alias'])) {
                    $this->model->datasets->{$datasetName}->alias = (string)$dataSetAttributes['alias'];
                } else {
                    $this->model->datasets->{$datasetName}->alias = $datasetName;
                }

                if (isset($dataSetAttributes['description'])) {
                    $this->model->datasets->{$datasetName}->description = (string)$dataSetAttributes['description'];
                } else {
                    $this->model->datasets->{$datasetName}->description = $this->model->datasets->{$datasetName}->alias;
                }
            }
        }
        
        // Model Dataset
        $this->model->dataset = new \stdClass();
        $this->model->dataset->dataset                = $this->modelObject->getDataset();
        $this->model->dataset->datasetAlias           = $this->modelObject->getDatasetAlias();
        $this->model->dataset->datasetDescription     = $this->modelObject->getDatasetDescription();
        $this->model->dataset->dataSetColumns         = $this->modelObject->getDataSetColumns();
        $this->model->dataset->dataSetFilter          = $this->modelObject->getDataSetFilter();
        $this->model->dataset->dataSetFilterId        = $this->modelObject->getDataSetFilterId();
        $this->model->dataset->dataSetCurrentBinder   = $this->modelObject->getDataSetCurrentBinder();
        $this->model->dataset->dataSetBinderGroup     = $this->modelObject->getDataSetBinderGroup();
        $this->model->dataset->dataSetGroup           = $this->modelObject->getDataSetGroup();
        $this->model->dataset->dataSetOrder           = $this->modelObject->getDataSetOrder();
        $this->model->dataset->dataSetLimit           = $this->modelObject->getDataSetLimit();
        $this->model->dataset->dataSetSums            = $this->modelObject->getDataSetSums();
        $this->model->dataset->dataSetConcat          = $this->modelObject->getDataSetConcat();
        $this->model->dataset->datasetColumnCount     = $this->modelObject->getDatasetColumnCount();
        $this->model->dataset->dataSetClosingBrackets = $this->modelObject->getDataSetClosingBrackets();
        $this->model->dataset->dataSetOpeningBrackets = $this->modelObject->getDataSetOpeningBrackets();
        $this->model->dataset->dataSetReturn          = $this->modelObject->getDataSetReturn();
        
        // Model Expression
        $this->model->expression = new \stdClass();
        $this->model->expression->params              = $this->modelObject->getExpressionParams();
        
        // Model Results
        $this->model->results = new \stdClass();
        $this->model->results->gotResults             = $this->modelObject->getGotResults();
        $this->model->results->collection             = $this->modelObject->getResults();
        $this->model->results->foundDouble            = $this->modelObject->getFoundDouble();
        $this->model->results->sqlQuery               = $this->modelObject->getSqlQuery();
        
        // Model Logs
        $this->model->logs = new \stdClass();
        $this->model->logs->sqlQuery                  = $this->modelObject->getSqlQuery();
        $this->model->logs->logQuery                  = $this->modelObject->getLogQuery();
        $this->model->logs->lastInsertId              = $this->modelObject->getLastInsertId();
        $this->model->logs->prevDataSetFilterId       = $this->modelObject->getPrevDataSetFilterId();
        
        // Model Post Processing
        $this->model->postProcessing = new \stdClass();
        $this->model->postProcessing->triggers        = $this->modelObject->getTriggers();
        $this->model->postProcessing->modelMold       = $this->modelObject->getModelMold();
        return $this->model;
    }
    
    private function setModelColumnsAsProperties() {
        $this->properties = new \stdClass();
        foreach($this->model->definition->columns as $property) {
            $property = str_replace($this->model->definition->table.'.','',$property);
            $this->properties->{$property} = null;
        }
    }
    
    
    // ----------- GETTERS
    
    // Return all the model details
    public function getModelDetails() {
        return $this->model;
    }
    
    public function getModelName() {
        return $this->model->definition->model;
    }
    
    public function getModelDefinition() {
        return $this->model->definition;
    }
    
    public function getModelSettings() {
        return $this->model->settings;
    }
    
    public function getModelValidation() {
        return $this->model->validation;
    }
    
    public function getModelSelect() {
        return $this->model->select;
    }
    
    public function getModelDataset () {
        return $this->model->dataset;
    }
    
    public function getModelExpression () {
        return $this->model->expression;
    }
    
    public function getModelResults () {
        return $this->model->results;
    }
    
    public function getModelLogs () {
        return $this->model->logs;
    }
    public function getModelPostProcessing () {
        return $this->model->postProcessing;
    }
    
    public function getModelConnection() {
        return $this->model->settings->connection;
    }
}
