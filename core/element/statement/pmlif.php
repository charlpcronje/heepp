<?php
namespace core\element\statement;
use core\Element;
use core\system\data;

class pmlif extends Element {
    public $isset;
    public $isnull;
    public $empty;
    public $dataexists;
    public $notempty;
    public $operator;
    public $value;
    public $equalto;
    public $notequalto;
    public $statement;
    public $fileexists;
    public $fileexist;
    public $filedontexist;
    public $morethan;
    public $greaterthan;
    public $ie;
    public $typeof;
    public $contains;
    public $notcontains;
    public $result = false;
    public $route;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        if (isset($this->dataexists)) {
            $this->result = (bool)data::exist($this->dataexists);
            $this->result = true;
        }

        if (isset($this->isset)) {
            $this->result = true;
        }
        
        if (isset($this->isnull)) {
            if ($this->isnull == null) {
                $this->result = true;
            }
        }
        
        if (isset($this->empty) && empty($this->empty)) {
            $this->result = true;
        }

        if (isset($this->notempty) && !empty($this->notempty)) {
            $this->result = true;
        }

        if (isset($this->equalto)) {
            if ($this->value == $this->equalto) {
                $this->result = true;
            }
        }

        if (isset($this->notequalto)) {
            if ($this->value != $this->notequalto) {
                $this->result = true;
            }
        }

        if (isset($this->statement) && eval("return $this->statement;")) {
            $this->result = true;
        }

        if (isset($this->fileexist) || isset($this->fileexists)) {
            if (isset($this->fileexist)) {
                $file = $this->fileexists = $this->fileexist;
            }
            if (file_exists(env('project.path').$this->fileexists) && !is_dir(env('project.path').$this->fileexists)) {
                $this->result = true;
            }
        }

        if (isset($this->morethan) || isset($this->greaterthan)) {
            if (isset($this->morethan)) {
                $this->greaterthan = $this->morethan;
            }
            if ($this->value > $this->greaterthan) {
                $this->result = true;
            }
        }

        if (isset($this->filedontexist)) {
            if (!file_exists(env('project.path').$this->filedontexist) || is_dir(env('project.path').$this->filedontexist)) {
                $this->result = true;
            }
        }

        if (isset($this->ie)) {
            if (preg_match('/MSIE\s(?P<v>\d+)/i', @$_SERVER['HTTP_USER_AGENT'], $B) && $B['v'] <= $this->ie) {
                $this->result = true;
            }
        }

        if (isset($this->equalto)) {
            if ($this->value == $this->equalto) {
                $this->result = true;
            }
        }

        if (isset($this->contains)) {
            if (strpos($this->value,$this->contains) !== false) {
                $this->result = true;
            }
        }

        if (isset($this->notcontains)) {
            if (strpos($this->value,$this->notcontains) === false) {
                $this->result = true;
            }
        }
        
        if (isset($this->route)) {
            if ($this->route == data::get('app.request.options.route')) {
                $this->result = true;
            }
        }

        /* Possible Types:
         * 'boolean','integer','double','string' */
        if (isset($this->typeof)) {
            $type = null;
            if (is_numeric($this->typeof)) {
                $type = 'integer';
                //if ($type == $this->equalto) {
                //    $this->result = true;
                //}
            }
            if(is_bool($this->typeof)) {
                $type = 'boolean';
                //if ($type == $this->equalto) {
                //    $this->result = true;
                //}
            }
            if(is_float($this->typeof)) {
                $type = 'double';
                //if ($type == $this->equalto) {
                //    $this->result = true;
                //}
            }

            if ($type == null) {
                $type = 'string';
            }

            if ($type == $this->equalto) {
                $this->result = true;
            }
        }

        if ($this->result) {
            return $this->child;
        }
        return false;
    }
}
