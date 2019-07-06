<?php
use core\Heepp;

class UserPermissions extends Heepp {
    public function __construct () {
        parent::__construct (__CLASS__);
    }

    public function getPermissions() {
        $file = file_get_contents(__DIR__.DS.'controllers.xml');
        $xml = simplexml_load_string('<root>'.$file.'</root>');
        $json = json_encode($xml);
        return json_decode($json);
    }
}
