<?php
namespace core\system\traits\core;
use core\extension\database\Model;
use core\system\env;

trait AccessTraits {
    private $controller;
    private $controllerConfig;
    private $controllerConfigPath;
    private $method;
    private $isRemoteFile              = false;
    private $resetSessionIfNotSingedIn = false;
    private $setFilemtimeInSession     = true;
    
    public function noAccess() {
        if (!$this->isSignedIn()) {
            return true;
        }
    }

    public function isSignedIn($resetSessionOnFail = true) {
        if ($this->sessionKeyExist('user.id')) {
            return true;
        }
        if ($this->resetSessionIfNotSingedIn) {
            unset($_SESSION['heepp'],$this->output->session);
            $this->refreshPage();
        }
        return false;
    }
    
    private function checkFilemtimeExists() {
        if ($this->setFilemtimeInSession) {
            return $this->session(env('project.name').'.controllers.config.mtime') !== null;
        }
        return $this->dataKeyExist(env('project.name').'.controllerConfig.mtime');
    }
    
    private function loadControllerConfig() {
        // Set data key so that the file mtime is only checked once every load and not for each controller being loaded
        if ($this->checkFilemtimeExists()) {
            if ($this->isRemoteFile) {
                // To save load time the filemtime can be set in the session.
                if ($this->setFilemtimeInSession) {
                    $this->session(env(project.name).'.controllers.config.mtime',remoteFilemtime($this->controllerConfigPath));
                } else {
                    $this->setData(env(project.name).'.controllers.config.mtime',remoteFilemtime($this->controllerConfigPath));
                }
            } else {
                $this->setData(env(project.name).'.controllers.config.mtime',filemtime($this->controllerConfigPath));
            }
        }
        
        // If the file mtime did not change and the session key exists then just load from session
        if ($this->setFilemtimeInSession) {
            if ($this->session(env('project.name').'.controllers.config.mtime') !== null) {
                $this->controllerConfig = $this->session(env('project.name').'.controllers.config.controllers');
            } else {
                if (!empty($this->controllerConfigPath)) {
                    $this->controllerConfig = json_decode(json_encode(simplexml_load_string('<root>'.file_get_contents($this->controllerConfigPath).'</root>')));
                } else {
                    $this->controllerConfig = json_decode(json_encode(simplexml_load_string('<root></root>')));
                }
            }
        } else {
            if ($this->session(env('project.name').'.controllers.config.mtime') !== null && $this->session(env('project.name').'.controllerConfig.mtime') == $this->getData(project.name.'.controllerConfig.mtime')) {
                $this->controllerConfig = $this->session(env('project.name').'.controllers.config.controllers');
            } else {
                $this->controllerConfig = json_decode(json_encode(simplexml_load_string(file_get_contents($this->controllerConfigPath))));
            }
        }
    }
    
    private function getControllerConfigURL() {
        if (env::exists('controller.config.url')) {
            $this->controllerConfigPath = env('controller.config.url');
            $this->isRemoteFile = true;
        } elseif (file_exists(env('project.controllers.path').'controllers.xml')) {
            $this->controllerConfigPath = env('project.controllers.path').'controllers.xml';
        }
    }

    public function hasAccess($method = null) {
        $this->session(env('project.name').'.controllers.config.mtime');
        $this->getControllerConfigURL();
        $this->loadControllerConfig();
        // Get current class name
        $this->controller = str_replace('\\','.',get_class($this));
        if (!isset($this->controllerConfig->{$this->controller}) || (!isset($this->controllerConfig->{$this->controller}->{'@attributes'}->auth) && $this->controllerConfig->{$this->controller}->{'@attributes'}->auth == 'true')) {
            if (isset($this->controllerConfig->{$this->controller}) && $this->controllerConfig->{$this->controller}->{'@attributes'}->status != 'active') {
                $this->setNotify('warning','<strong>'.$this->controllerConfig->{$this->controller}->{'@attributes'}->alias.'</strong> is currently under construction');
                return false;
            }
        }
        
        if (!$this->isSignedIn()) {
            return true;
        }
        // If the current controller being called is not configured on the controller config the user automatically gets access 
        if (!isset($this->controllerConfig->{$this->controller})) {
            return true;
        }
        
        // Check if the current controller is active
        if ($this->controllerConfig->{$this->controller}->{'@attributes'}->status != 'active') {
            $this->setNotify('warning','<i class="fa fa-'.$this->controllerConfig->{$this->controller}->{'@attributes'}->icon.'"></i><strong> '.$this->controllerConfig->{$this->controller}->{'@attributes'}->alias.'</strong> is currently under construction');
            return false;
        }
        
        $alias = $this->controllerConfig->{$this->controller}->{'@attributes'}->alias;
        $model = new Model();
        if (!$model->modelExists('userPermission') && !$model->modelExists('/sc/userPermission')) {
            $this->setNotify('error','Missing Model "/sc/userPermission"');
            return false;
        }
        
        $model->setSaveLog(false);
        $model->loadDataSet('byUserAndController')->userId($this->session('user.id'))->controller($this->controller)->runDataSet();

        // Check in database if user has access to current controller
        if (!$model->getGotResults() && $this->controllerConfig->{$this->controller}->{'@attributes'}->auth == 'true') {
            $this->setNotify('error','You do not have access to: <i class="fa fa-'.$this->controllerConfig->{$this->controller}->{'@attributes'}->icon.'"></i><strong> '.$alias.'</strong>');
            return false;
        }

        // If the method is not set then stop checking further permissions
        if (!isset($method)) {
            return true;
        }
        $this->method = $method;

        if (!isset($this->controllerConfig->{$this->controller}->methods->{$this->method})) {
            return true; 
        }

        // Check if the controller method is active
        if ($this->controllerConfig->{$this->controller}->methods->{$this->method}->{'@attributes'}->status != 'active') {
            $this->setNotify('info','Method: <i class="fa fa-'.$this->controllerConfig->{$this->controller}->{'@attributes'}->icon.'"></i><strong> '.$this->controllerConfig->{$this->controller}->methods->{$this->method}->{'@attributes'}->alias.'</strong> is currently under construction');
            return false;
        }

        if (!$this->controllerConfig->{$this->controller}->methods->{$this->method}) {
            return true;
        }

        $model->loadDataSet('byUserControllerAndMethod')->userId($this->session('user.id'))->controller($this->controller)->method($this->method)->runDataSet();
        // Check in database if user has access to set controller method
        if ($model->getGotResults()) {
            if (LOG) {
                $this->logBegin($this->method,$this->input('params'));
            }
            return true;
        }
        $this->setNotify('error','You do not have access to: <i class="fa fa-'.$this->controllerConfig->{$this->controller}->{'@attributes'}->icon.'"></i><strong> '.$this->controllerConfig->{$this->controller}->methods->{$this->method}->{'@attributes'}->alias.'</strong>');
    }
}
