<?php
namespace core\system\traits\core;
use core\extension\database\Model;
use core\extension\ui\coreFO;
/**
 * @author Charl Cronje <charlcp@gmail.com>
 * @date 01 Dec 2015
 * @time 4:31:40 AM
 */
trait ModelTraits {
//    public function setModelData($modelName,$dataSet,$params = array(),$returnType = 'object',$loadDefaults = true) {
//        $this->output->model->$modelName = new Model($modelName);
//        $this->output->model->$modelName->useDefaults = $loadDefaults;
//        $this->output->model->$modelName->dataSetReturn = $returnType;
//        $this->output->model->$modelName->loadDataSet($dataSet);
//        foreach($params as $key => $value) {
//            $this->output->model->$modelName->$key($value);
//        }
//        $this->output->model->$modelName->runDataSet();
//        $this->output->data->$modelName = $this->output->model->$modelName->results;
//
//        return $this->output->data->$modelName;
//    }
    
    public function fromModel($modelName,$dataSet,$params = [],$returnType = 'object',$loadDefaults = true) {
        return (new Model($modelName))
                    ->useDefaults(true)
                    ->dataSetReturn($returnType)
                    ->loadDataSet($dataSet)
                    ->setParams($params)
                    ->runDataSet()
                    ->results();
     }
    
}
