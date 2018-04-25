<?php
namespace core;

class Heepp {
    use system\traits\core\SetterTraits;
    use system\traits\core\GetterTraits;
    use system\traits\core\DataTraits;
    use system\traits\core\SessionTraits;
    use system\traits\core\InputTraits;
    use system\traits\core\AccessTraits;
    use system\traits\core\LogTraits;
    use system\traits\core\RequestTraits;
    public $output;
    public $parentClass;

    public function __construct($className = null) {
        $this->output = Output::getInstance();
        if (!$this->dataKeyExist('env')) {
            $this->setEnvData();
        }

        $this->setOutputDefaults();
        $this->setClassName($className);
        $this->setCacheConstants();
        if (!$this->hasAccess()) {
            throw new \Exception('Denied');
        }
    }

    private function setEnvData() {
        foreach($_ENV as $key => $value) {
            $this->setData('env.'.$key,$value);
        }
    }

    private function setCacheConstants() {
        if (!defined('MEM_CACHE_HOST') && $this->dataKeyExist('app.system.cache.host') && $this->dataKeyExist('app.system.cache.port')) {
            define('MEM_CACHE_HOST',$this->getData('app.system.cache.host'));
            define('MEM_CACHE_PORT',$this->getData('app.system.cache.port'));
        }
    }

    public function cache($key,$value = null) {
        if(class_exists('Memcache')){
            $memcache = memcache_connect(MEM_CACHE_HOST,MEM_CACHE_PORT);
            if (isset($key) && !isset($value)) {
                // Get value from key
                return $memcache->get($key);
            }

            if (isset($key) && isset($value)) {
                // Set value to key
                return $memcache->set($key,$value);
            }
        }
    }

    public function cached($key) {
        if(class_exists('Memcache')){
            $memcache = memcache_connect(MEM_CACHE_HOST,MEM_CACHE_PORT);
            if ($memcache->get($key)) {
                return true;
            }
            return false;
        }
    }

    public function uncache($key) {
        $memcache = memcache_connect(MEM_CACHE_HOST,MEM_CACHE_PORT);
        $memcache->delete($key);
    }

    public function __set($name,$value) {
        if (!isset($this->output)) {
            $this->output = new \stdClass();
        }

        if (!isset($this->output->data)) {
            $this->output->data = new \stdClass();
        }

        if (isset($this->output->ui->className)) {
            $this->output->{$this->output->className}->$name = $value;
        }
    }

    public function __get($name) {
        if (isset($this->output->$name)) {
            return $this->output->$name;
        }
    }

    public function refreshPage() {
        $tag = new Element();
        $this->appendHtml('head',(new Element)->setElement('script')->add('window.location.reload();')->render());
    }

    public function __destruct () {
        if (isset($_SESSION) && isset($this->output)) {
            if (isset($this->output->session)) {
                $_SESSION['core'] = $this->output->session;
            }
        }
    }

    //protected function render() {
    //    $this->output->render[] = $html;
    //}

    private function processRedirectOptions($options) {
        foreach($options as $optionKey => $optionValue) {
            switch(strtolower($optionKey)) {
                case 'message':
                    if (!isset($optionValue['type'])) {
                        $optionValue['type'] = 'info';
                    }
                    $optionValue['timeout'] = 60;
                    $this->session('app.redirect.message',[
                        'type' => $optionValue['type'],
                        'message' => $optionValue['message'],
                        'timeout' => $optionValue['timeout']
                    ]);
                break;
            }
        }
    }

    private function setRedirectInputs() {
        //foreach($this->input() as $key => $value) {
        //    $this->session('app.redirect.input.'.$key,$value);
        //}
    }

    private function setRedirectParams($options = []) {
        if (is_array($options) && count($options) > 0) {
            $this->processRedirectOptions($options);
        }

        if (isset($_GET) && isset($_POST)) {
            $this->setRedirectInputs();
        }
    }

    public function redirect($route,$options = []) {
        $this->setRedirectParams($options);
        header('Location: '.env('domain').$route);
    }

    public static function __callStatic($method, $arguments) {
        return (new Heepp())->$method($arguments);
    }

    // public function __isset($property = null) {
    //     if (isset($this->{$property})) {
    //         return true;
    //     }
    //     return false;
    // }
}
