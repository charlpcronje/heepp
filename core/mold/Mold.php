<?php
namespace core\mold;
use core\Heepp;
use core\system\env;

class Mold extends Heepp {
    protected $template = '${_start_}
${_EBS_}${_generator_} 
${_datetime_}${_EBE_}

${_header_}

${_body_}

${_footer_}

${_end_}';
    protected $templateParams = [
        '_ELS_'         => '// ',                   // Escape Line Start
        '_ELE_'         => '',                      // Escape Line End
        '_EBS_'         => "/*\n",                  // Escape Block Start
        '_EBE_'         => "\n*/",                  // Escape Block End
        '_start_'       => '',                      // Mold Start
        '_generator_'   => 'Molded by core',
        '_datetime_'    => CURRENT_TIMESTAMP,
        '_header_'      => '',
        '_body_'        => '',                      // Mold Body
        '_footer_'      => '',
        '_end_'         => ''                       // Mold End
    ];
    protected $name;
    protected $group; // php, view, xml
    protected $type;  // iterator ..
    protected $moldOutput;
    
    protected $options = [
        'saveOutput'   => true,
        'formatOutput' => false,
        'formatter'    => [], // ['class' => 'php class name together with namespace','method' => 'method in class to call' or 'staticMethod' => 'static method to call']
        'backupOld'    => false,
        'savePath'     => ''
    ];
    protected $filename;
    
    public function __construct($name,$options = [],$templateParams = []) {
        parent::__construct();
        if (!isset($options['savePath'])) {
            $this->options['savePath'] = env('project.molds.path');
        }
        if (!isset($this->filename)) {
            $this->filename = $this->name.'.mold';
        }
        $this->options = array_merge($this->options,$options);
        $this->templateParams = array_merge($this->templateParams,$templateParams);
        $this->prepareOutput();
    }
    
    private function replaceParamsInString($string,$params) {
        foreach($params as $key => $value) {
            if (is_array($value)) {
                $valueString = '';
                foreach($value as $subValue) {
                    $valueString .= $subValue."\n";
                }
                $string = str_replace('${'.$key.'}',$valueString, $string);
            } else {
                $string = str_replace('${'.$key.'}',$value, $string);
            }
        }
        return $string;
    }
    
    private function prepareOutput() {
        $this->moldOutput = $this->replaceParamsInString($this->template,$this->templateParams);
    }
    
    public function getFile() {
        return $this->filename;
    }
    
    public function getOutput() {
        return $this->moldOutput;
    }
    
    private function saveOutputToFile() {
        if (!file_exists($this->options['savePath']) || !is_dir($this->options['savePath'])) {
            mkdir($this->options['savePath'],0777, true);
        }
        if (substr($this->options['savePath'],-1,1) != DS) {
            $this->options['savePath'] .= DS;
        }
        if ($this->options['backupOld']) {
            $backupFileName = date('YmdHis').$this->filename;
            if (!file_exists(env('project.molds.backup.path')) || !is_dir(env('project.molds.backup.path'))) {
                mkdir(env('project.molds.backup.path'),0777, true);
            }
            if (file_exists($this->options['savePath'].$this->filename)) {
                rename($this->options['savePath'].$this->filename,env('project.molds.backup.path').$backupFileName);
            }
        }
        file_put_contents($this->options['savePath'].$this->filename,$this->moldOutput);
    }
    
    public function render() {
        $this->moldOutput = $this->replaceParamsInString($this->moldOutput,$this->templateParams);
        if ($this->options['formatOutput']) {
            if (isset($this->options['formatter']['staticMethod'])) {
                $formatterClass = $this->options['formatter']['class'];
                $fomatterMethod = $this->options['formatter']['staticMethod'];
                $this->moldOutput = $formatterClass::$fomatterMethod($this->moldOutput);
            } elseif(isset($this->options['formatter']['method'])) {
                $formatterClass = $this->options['formatter']['class'];
                $fomatterMethod = $this->options['formatter']['method'];
                $this->moldOutput = (new $formatterClass())->$fomatterMethod($this->moldOutput);
            }
        }
            
        if ($this->options['saveOutput']) {
            $this->saveOutputToFile();
        }
        return $this;
    }
}
