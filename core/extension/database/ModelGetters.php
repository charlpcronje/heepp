<?php
namespace core\extension\database;

trait ModelGetters {
    public function getConnection() {
        return $this->_connection;
    }
    
    public function getSaveLog() {
        return $this->saveLog;
    }
    
    public function getSaveSelectLog() {
        return $this->saveSelectLog;
    }
    
    public function getUseDefaults() {
        return $this->useDefaults;
    }
    
    public function getIgnoreTriggers() {
        return $this->ignoreTriggers;
    }
    
    public function getReturn() {
        return $this->return;
    }
    
    public function getModel() {
        return $this->model;
    }
    
    public function getModelAlias() {
        return $this->modelAlias;
    }
    
    public function getTable() {
        return $this->table;
    }
    
    public function getKey() {
        return $this->key;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getCombKey() {
        return $this->combKey;
    }
    
    public function getConstants() {
        return $this->constants;
    }
    
    public function getXml() {
        return $this->xml;
    }
    
    public function getValidateValues() {
        return $this->validateValues;
    }
    
    public function getValidations() {
        return $this->validations;
    }
    
    public function getRequirements() {
        return $this->requirements;
    }
    
    public function getCheckRequired() {
        return $this->checkRequired;
    }
    
    public function getDefaults() {
        return $this->defaults;
    }
    
    public function getColumns() {
        return $this->columns;
    }
    
    public function getObjectProperties() {
        return $this->objectProperties;
    }
    
    public function getJoins() {
        return $this->joins;
    }
    
    public function getFilter() {
        return $this->filter;
    }
    
    public function getGroup() {
        return $this->group;
    }
    
    public function getOrder() {
        return $this->order;
    }
    
    public function getLimit() {
        return $this->limit;
    }
    
    public function getDataSet() {
        return $this->dataSet;
    }
    
    public function getDatasetAlias() {
        return $this->datasetAlias;
    }
    
    public function getDatasetDescription() {
        return $this->datasetDescription;
    }
    
    public function getDataSetColumns() {
        return $this->dataSetColumns;
    }
    
    public function getDataSetFilter() {
        return $this->dataSetFilter;
    }
    
    public function getDataSetFilterId() {
        return $this->dataSetFilterId;
    }
    
    public function getDataSetCurrentBinder() {
        return $this->dataSetCurrentBinder;
    }
    
    public function getDataSetBinderGroup() {
        return $this->dataSetBinderGroup;
    }
    
    public function getDataSetGroup() {
        return $this->dataSetGroup;
    }
    
    public function getDataSetOrder() {
        return $this->dataSetOrder;
    }
    
    public function getDataSetLimit() {
        return $this->dataSetLimit;
    }
    
    public function getDataSetSums() {
        return $this->dataSetSums;
    }
    
    public function getDataSetConcat() {
        return $this->dataSetConcat;
    }
    
    public function getDatasetColumnCount() {
        return $this->datasetColumnCount;
    }
    
    public function getDataSetClosingBrackets() {
        return $this->dataSetClosingBrackets;
    }
    
    public function getDataSetOpeningBrackets() {
        return $this->dataSetOpeningBrackets;
    }
    
    public function getDataSetReturn() {
        return $this->dataSetReturn;
    }
    
    public function getExpressionParams() {
        return $this->expressionParams;
    }
    
    public function getResults() {
        return $this->results;
    }
    
    public function getResult($key = null) {
        if (isset($key) && isset($this->results[$key]) && $this->getGotResults()) {
            return $this->results[$key];
        } else {
            return $this->results;
        }
    }
    
    public function getGotResults() {
        return $this->gotResults;
    }
    
    public function getFoundDouble() {
        return $this->foundDouble;
    }
    
    public function getSqlQuery() {
        return $this->sqlQuery;
    }
    
    public function getLogQuery() {
        return $this->logQuery;
    }
    
    public function getLastInsertId() {
        return $this->lastInsertId;
    }
    
    public function getPrevDataSetFilterId() {
        return $this->prevDataSetFilterId;
    }
    
    public function getTriggers() {
        return $this->triggers;
    }
    
    public function getModelMold() {
        return $this->modelMold;
    }
}
