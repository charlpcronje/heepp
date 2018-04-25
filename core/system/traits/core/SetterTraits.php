<?php
namespace core\system\traits\core;
use core\extension\ui\view;

trait SetterTraits {
    public function setView($view,$target = 'none',$viewPath = null) {
        if (is_object($view)) {
            $property = md5($view,true);
        } else {
            $property = str_replace('/','.',$view);
        }
        $view = new view($view,$viewPath);

        if ($target != 'none') {
            $this->setHtml($target,$view->html);
        }
        return $view->html;
    }

    public static function setHeader($param,$setting) {
        header($param.': '.$setting);
    }

    public static function contentType($type) {
        self::setHeader('Content-Type',$type);
    }

    public function setClassName($className = null) {
        if (isset($className)) {
            if (!empty($className)) {
                $this->output->className = $className;
                $this->output->ui->$className = new \stdClass();
            }
        }
    }

    public function setOutputDefaults() {
        if (!isset($this->output)) {
            $this->output = new \stdClass();
        }
        if (!isset($this->output->html)) {
            $this->output->html = [];
        }
        if (!isset($this->output->data)) {
            $this->output->data = new \stdClass();
        }
        if (!isset($this->output->model)) {
            $this->output->model = new \stdClass();
        }
        if (!isset($this->output->ui)) {
            $this->output->ui = new \stdClass();
        }
        if (!isset($this->output->{$this->output->className})) {
            if (!isset($this->output->{$this->output->className})) {
                if (!isset($this->output->className) || empty($this->output->className)) {
                    $this->output->className = 'Heepp';
                }
                $this->output->{$this->output->className} = 'Heepp';
            }
            $this->output->{$this->output->className} = __CLASS__;
        }
        if (!isset($this->output->constant)) {
            $this->output->constant = (object)get_defined_constants(true)['user'];
        }
        if (!isset($this->output->session) && !empty($_SESSION)) {
            // Encoding and then decoding to json converts a variable to an stdClass object
            if (!isset($_SESSION['core'])) {
                $_SESSION['core'] = new \stdClass();
            }
            $this->output->session = $_SESSION['core'];
        }
        if (isset($_SESSION)) {
            $this->output->data->session = $_SESSION['core'];
            $this->output->session       = $_SESSION['core'];
        }
    }

    // Render Functions
    public function setEvent($event,$target,$callBack = null) {
        $this->output->ui->$event[$target] = $callBack;
    }

    public function setScript($path) {
        $this->output->script[] = $path;
    }

    public function setHtml($target,$html = null) {
        $this->output->ui->html[] = [
            'method' => 'replace',
            'target' => $target,
            'html'   => $html
        ];
    }

    public function removeHtml($target) {
        $this->output->ui->html[] = [
            'method' => 'remove',
            'target' => $target
        ];
    }

    public function appendHtml($target,$html) {
        $this->output->ui->html[] = [
            'method' => 'append',
            'target' => $target,
            'html'   => $html
        ];
    }
    
    public function setValue($target,$value) {
        $this->output->ui->value[] = [
            'target' => $target,
            'value'  => $value
        ];
    }

    public function prependHtml($target,$html) {
        $this->output->ui->html[] = [
            'method' => 'prepend',
            'target' => $target,
            'html'   => $html
        ];
    }

    public function setOffcanvas($heading,$body,$width = '240px') {
        $this->output->ui->offcanvas[] = [
            'heading' => $heading,
            'body'    => $body,
            'width'   => $width
        ];
    }

    public function setHelper($helper) {
        $this->output->ui->helper[] = $helper;
    }

    public function setError($error) {
        $this->output->ui->notify = [
            'type'    => 'error',
            'message' => $error
        ];
    }

    public function setNotify($type,$message) {
        $this->output->ui->notify = [
            'type'    => $type,
            'message' => $message
        ];
    }

    public function setClick($elem) {
        $this->output->ui->click[] = [
            'selector' => $elem
        ];
    }
    
    public function setOptions($target,$options,$valueKey,$optionKey,$selected = null) {
        $html = '';
        foreach($options as $option) {
            $select = '';
            if (isset($selected)) {
                if ($option->{$valueKey} == $selected) {
                    $select = 'selected="selected"';
                }
            }
            $html .= '<option value="'.$option->{$valueKey}.'" '.$select.'>'.$option->{$optionKey}.'</option>';
        }
        $this->setHtml($target,$html);
    }

    public function setCallback($callback,$arguments = null,$context = 'window') {
        $this->output->ui->callback[] = [
            'callback'  => $callback,
            'arguments' => $arguments,
            'context'   => $context
        ];
    }
    
    public function setVar($variable,$value,$context = 'window') {
        $this->output->ui->var[] = [
            'variable' => $variable,
            'value'    => $value,
            'context'  => $context
        ];
    }

    public function setJson($method,$json) {
        $this->output->ui->json[$method] = $json;
    }

    public function setTagAttr($target,$attr,$value) {
        $this->output->ui->attr[] = [
            'target' => $target,
            'attr'   => $attr,
            'value'  => $value
        ];
    }

    public function setConsole($data,$description = 'data') {
        $this->output->ui->console[] = [
            'description' => $description,
            'data'        => $data
        ];
    }

    public function setHash($hash) {
        $this->output->hash = $hash;
    }

    public function setStyle($target,$style,$value) {
        $this->output->ui->style[] = [
            'target' => $target,
            'style'  => $style,
            'value'  => $value
        ];
    }

    public function refreshPage() {
        $this->output->refreshPage = '1';
    }

    public function addClass($target,$class) {
        $this->output->ui->class[] = [
            'method' => 'add',
            'target' => $target,
            'class'  => $class
        ];
    }
    
    public function removeClass($target,$class) {
        $this->output->ui->class[] = [
            'method' => 'remove',
            'target' => $target,
            'class'  => $class
        ];
    }

    public function setConfirm($heading,$message,$action) {
        $this->output->ui->confirm = [
            'heading' => $heading,
            'message' => $message,
            'action'  => $action
        ];
    }

    public function setHide($target) {
        $this->output->ui->hide = [
            'target' => $target
        ];
    }

    public function setShow($target) {
        $this->output->ui->show = [
            'target' => $target
        ];
    }

    public function setRedirect($url,$method) {
        $this->output->ui->redirect = [
            'url' => $url,
            'method' => $method
        ];
    }
}
