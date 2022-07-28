<?php
//$cfg = new Config("/etc/config.ini");

namespace core\extension\parser\XML;
Class INIConfig extends \core\extension\Extension {
    protected $iniPath;
    public $cfg = array();

    public function __construct($iniPath) {
        parent::__construct();
        $this->iniPath = $iniPath;
        $this->loadINI();
    }

    protected function loadINI() {
        if (file_exists($this->iniPath)) {
            $settings = str_replace(' ','',parse_ini_string(file_get_contents($this->iniPath),true));
            foreach($settings as $key => $value) {
                $this->cfg[$key] = str_replace(' ','',$value);
            }
        } else {
            throw new \Exception('INI File '.$this->iniPath.' was not found');
        }
    }
    
    //echo $cfg->get("section.key");
    public function get($key) {
        $splitSectionAndKey = explode('.',$key);
        if (count($splitSectionAndKey) > 1) {
            return $this->cfg[$splitSectionAndKey[0]][$splitSectionAndKey[1]];
        } else {
            return $this->cfg[$splitSectionAndKey[0]];
        }
    }
}

