<?php
namespace core\system\traits\core;

trait SessionTraits {
    public function sessionKeyExist($key) {
        return $this->dataKeyExist($key,$this->output->session);
    }

    public function session($dotName,$value = null) {
        if (isset($value)) {
            return $this->setData($dotName,$value,$this->output->session);
        }
        return $this->getData($dotName,null,$this->output->session);
    }

    /*
      * TO-DO: Add method so that I can call isset() on the session method
        function __isset ($name) {
            echo $name.'.';
        }
     */
}
