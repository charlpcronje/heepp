<?php
namespace core\element\method;

class logic extends \core\element\element {
    public $dataset1 = null;
    public $field1 = array();
    public $dataset2 = null;
    public $field2 = array();
    public $rows = 10;
    public $filter = null;
    public $value = null;
    public $record1 = null;
    public $record2 = null;
    public $operator = null;

    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    function getProperties() {
        $properties = array();
        $properties['dataset1'] = $this->dataset1;
        $properties['field1'] = $this->field1;
        $properties['dataset2'] = $this->dataset2;
        $properties['field2'] = $this->field2;
        $properties['rows'] = $this->rows;
        $properties['filter'] = $this->filter;

        if (isset($this->dataset1)) {
            $model = new Model($this->dataset1);
            if (isset($this->rows)) {
                $rows = $this->rows;
            }
            if (isset($this->filter)) {
                $filter[$this->field1] = $this->filter1;
            }
            if (isset($this->field1)) {
                $fields = array($this->field1);
            }
            $model->select('array',$fields,$filter,false,$rows);
            if ($model->gotResults) {
                $this->record1 = $model->results[0][$this->field1];
            }
        }

        if (isset($this->dataset2)) {
            $model = new Model($this->dataset1);
            if (isset($this->rows)) {
                $rows = $this->rows;
            }
            if (isset($this->filter)) {
                $filter[$this->field1] = $this->filter1;
            }
            if (isset($this->field2)) {
                $fields = array($this->field2);
            }
            $model->select('array',$fields,$filter,false,$rows);
            if ($model->gotResults) {
                $this->record2 = $model->results[0][$this->field2];
            }
            switch($this->operator) {
                case 'EQUAL':
                    if ($this->record1 == $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
                case 'NOT EQUAL':
                    if ($this->record1 != $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
                case 'GREATER THAN':
                    if ($this->record1 > $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
                case 'GREATER OR EQUAL':
                    if ($this->record1 >= $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
                case 'SMALLER THAN':
                    if ($this->record1 < $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
                case 'SMALLER OR EQUAL':
                    if ($this->record1 <= $this->record2) {
                        $properties['value'] = 'true';
                    } else {
                        $properties['value'] = 'false';
                    }
                break;
            }
        }
        return $properties;
    }

    function render() {
        $fo = new core\extension\ui\coreFO('logic.pml',$this);
        return $fo->html;
    }
}
