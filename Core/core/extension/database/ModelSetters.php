<?php
namespace core\extension\database;

trait ModelSetters {
    public function setSaveLog($saveLog = true) {
        $this->saveLog = $saveLog;
    }
    
    public function setSaveSelectLog($saveSelectLog = true) {
        $this->saveSelectLog = $saveSelectLog;
    }
    
    public function setUseDefaults($useDefaults = false) {
        $this->useDefaults = $useDefaults;
    }
    
    public function setIgnoreTriggers($ignoreTriggers = false) {
        $this->ignoreTriggers = $ignoreTriggers;
    }
    
    public function setReturn($return = 'object') {
        $this->return = $return;
    }
    
    public function setModel($model = null) {
        $this->model = $model;
    }
    
    public function setModelAlias($modelAlias = null) {
        $this->modelAlias = $modelAlias;
    }
    
    public function setTable($table = null) {
        $this->table = $table;
    }
    
    public function setKey($key = null) {
        $this->key = $key;
    }
    
    public function setPath($path = null) {
        $this->path = $path;
    }
    
    public function setCombKey($combKey = []) {
        $this->combKey = $combKey;
    }
    
    public function setConstants($constants = []) {
        $this->constants = $constants;
    }
    
    public function setXml($xml = null) {
        $this->xml = $xml;
    }
    
    public function setValidateValues($validateValues = true) {
        $this->validateValues = $validateValues;
    }
    
    public function setValidations($validations = []) {
        $this->validations = $validations;
    }
    
    public function setRequirements($requirements = []) {
        $this->requirements = $requirements;
    }
    
    public function setCheckRequired($checkRequired = true) {
        $this->checkRequired = $checkRequired;
    }
    
    public function setDefaults($defaults = []) {
        $this->defaults = $defaults;
    }
    
    public function setColumns($columns = null) {
        $this->columns = $columns;
    }
    
    public function AddObjectProperty($property,$value = null) {
        /* Convert $this->objectProperties from array to stdClass because $this->objectProperties is declared as [] in 
         * properties because you can't set a property as an instance of a stdClass in the object properties
         */
        if (is_array($this->objectProperties)) {
            $this->objectProperties = (object)$this->objectProperties;
        }
        
        // Check if property is set and has a value
        if (isset($this->objectProperties->$property) && !empty($this->objectProperties->$property)) {
            return $this;
        }
              
        if (isset($value)) {
            $this->objectProperties->{$property} = $value;
        } elseif($this->useDefaults) {
            if (isset($this->defaults[$property])) {
                $this->objectProperties->{$property} = $this->defaults[$property];
            }  else {
                $this->objectProperties->{$property} = null;
            }
        } else {
            $this->objectProperties->{$property} = null;
        }
        return $this;
    }
    
    public function setJoins($joins = []) {
        $this->joins = $joins;
    }
    
    public function setFilter($filter = []) {
        $this->filter = $filter;
    }
    
    public function setGroup($group = []) {
        $this->group = $group;
    }
    
    public function setOrder($order = []) {
        $this->order = $order;
    }
    
    public function setLimit($limit = []) {
        $this->limit = $limit;
    }
    
    public function setDataSet($dataSet = 'byId') {
        $this->dataSet = $dataSet;
    }
    
    public function setDatasetAlias($datasetAlias = null) {
        $this->datasetAlias = $datasetAlias;
    }
    
    public function setDatasetDescription($datasetDescription = null) {
        $this->datasetDescription = $datasetDescription;
    }
    
    public function setDataSetColumns($dataSetColumns = []) {
        $this->dataSetColumns = $dataSetColumns;
    }
    
    public function setDataSetFilter($dataSetFilter = []) {
        $this->dataSetFilter = $dataSetFilter;
    }
    
    public function setDataSetFilterId($dataSetFilterId = 0) {
        $this->dataSetFilterId = $dataSetFilterId;
    }
    
    public function setDataSetCurrentBinder($dataSetCurrentBinder = 'AND') {
        $this->dataSetCurrentBinder = $dataSetCurrentBinder;
    }
    
    public function setDataSetBinderGroup($dataSetBinderGroup = 1) {
        $this->dataSetBinderGroup = $dataSetBinderGroup;
    }
    
    public function setDataSetGroup($dataSetGroup = []) {
        $this->dataSetGroup = $dataSetGroup;
    }
    
    public function setDataSetOrder($dataSetOrder = []) {
        $this->dataSetOrder = $dataSetOrder;
    }
    
    public function setDataSetLimit($dataSetLimit = []) {
        $this->dataSetLimit = $dataSetLimit;
    }
    
    public function setDataSetSums($dataSetSums = []) {
        $this->dataSetSums = $dataSetSums;
    }
    
    public function setDataSetConcat($dataSetConcat = []) {
        $this->dataSetConcat = $dataSetConcat;
    }
    
    public function setDatasetColumnCount($datasetColumnCount = 0) {
        $this->datasetColumnCount = $datasetColumnCount;
    }
    
    public function setDataSetClosingBrackets($dataSetClosingBrackets = 0) {
        $this->dataSetClosingBrackets = $dataSetClosingBrackets;
    }
    
    public function setDataSetOpeningBrackets($dataSetOpeningBrackets = 0) {
        $this->dataSetOpeningBrackets = $dataSetOpeningBrackets;
    }
    
    public function setDataSetReturn($dataSetReturn = 'object') {
        $this->dataSetReturn = $dataSetReturn;
    }
    
    public function setExpressionParams($expressionParams = []) {
        $this->expressionParams = $expressionParams;
    }
    
    public function setResults($results = null,$setResultAs = 'array') {
        if ($setResultAs == 'array') {
            if (is_array($results)) {
                $this->results = $results;
            } else {
                $this->results[] = $results;
            }
        } else {
            $this->results = $results;
        }
        $this->isLoaded(true);
    }
    
    public function setGotResults($gotResults = false) {
        $this->gotResults = $gotResults;
    }
    
    public function setFoundDouble($foundDouble = false) {
        $this->foundDouble = $foundDouble;
    }
    
    public function setSqlQuery($sqlQuery = null) {
        $this->sqlQuery = $sqlQuery;
    }
    
    public function setLogQuery($logQuery = null) {
        $this->logQuery = $logQuery;
    }
    
    public function setLastInsertId($lastInsertId = null) {
        $this->lastInsertId = $lastInsertId;
    }
    
    public function setPrevDataSetFilterId($prevDataSetFilterId = []) {
        $this->prevDataSetFilterId = $prevDataSetFilterId;
    }
    
    public function setTriggers($triggers = []) {
        $this->triggers = $triggers;
    }
    
    public function setModelMold($modelMold = null) {
        $this->modelMold = $modelMold;
    }
}
