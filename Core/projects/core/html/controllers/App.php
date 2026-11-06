<?php
use core\Heepp;
use core\extension\ui\view;
use core\Output;
use core\system\handlers\ProjectLoader;

class App extends Console {

    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function getCoreStats() {
        return $this->coreStats;
    }

    public function getFileCount($path,$ignore = ['ext' => ''],$ignoreExt = [],$includeExt = []) {
        $size = 0;
        $ignore = array('.','..','cgi-bin','.DS_Store');
        $files = scandir($path,SCANDIR_SORT_ASCENDING);
        foreach($files as $t) {
            if(in_array($t, $ignore)) {
                continue;
            }
            if (is_dir(rtrim($path, '/') . '/' . $t)) {
                $size += $this->getFileCount(rtrim($path, '/') . '/' . $t);
            } else {
                $size++;
            }
        }
        return $size;
    }
}
