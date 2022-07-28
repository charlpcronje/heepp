<?php
namespace core\element\method;
use core\Element;

class data extends Element {
    public $dataset = null;
    public $field = array();
    public $rows = 10;
    public $filter = null;
    public $value = null;

    public function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    public function getProperties() {
        $properties = array();
        $properties['dataset'] = $this->dataset;
        $properties['field'] = $this->field;
        $properties['rows'] = $this->rows;
        $properties['filter'] = $this->filter;
        if (isset($this->dataset)) {
            $model = new Model($this->dataset);
            if (isset($this->rows)) {
                $rows = $this->rows;
            }
            if (isset($this->filter)) {
                $filter[$this->field] = $this->filter;
            }
            if (isset($this->field)) {
                $fields = array($this->field);
            }
            $model->select('array',$fields,$filter,false,$rows);
            if ($model->gotResults) {
                $this->value = $model->results[0][$this->field];
                $properties['value'] = nl2br($this->value);
            }
        }
        return $properties;
    }

    public function render() {
        return view::mold('data.pml',__DIR__);
    }
}
