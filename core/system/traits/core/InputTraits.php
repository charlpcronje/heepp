<?php
namespace core\system\traits\core;

trait InputTraits  {
   public function inputSet($inputKey) {
        if (is_array($inputKey) && count($inputKey) == 1) {
           $inputKey = (string)$inputKey[0];
        }
        if (isset($_GET[$inputKey])) {
            return true;
        }
        if (isset($_POST[$inputKey])) {
            return true;
        }
        return false;
    }

    public function input($inputKey = null,$default = null) {
        if (is_array($inputKey) && count($inputKey) == 1) {
            $inputKey = (string)$inputKey[0];
        }
        $value = null;
        // There is a $_GET and a POST with the same key the $_POST key will take precedence
        if (isset($_GET[$inputKey])) {
            $value = $_GET[$inputKey];
        }

        if (isset($_POST[$inputKey])) {
            $value = $_POST[$inputKey];
        }

        if (!isset($_GET[$inputKey]) && isset($default)) {
            $_GET[$inputKey] = $default;
        }
        
        if (!isset($_POST[$inputKey]) && isset($default)) {
            $_POST[$inputKey] = $default;
        }
        
        if (!isset($_POST[$inputKey]) && !isset($_GET[$inputKey]) && isset($default)) {
            $value = $default;
        }
        
        if (!isset($_POST[$inputKey]) && !isset($_GET[$inputKey]) && !isset($default)) {
            unset($_GET['controller']);
            unset($_GET['params']);
            if (isset($_GET) && !empty($_GET)) {
                $value = $_GET;
            }
            
            if(isset($_POST) && !empty($_POST)) {
                $value = $_POST;
            }
        }
        return $value;
    }
}
