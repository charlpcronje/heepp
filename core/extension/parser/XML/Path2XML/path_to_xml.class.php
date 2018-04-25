<?php

/* PathToXml Class
* @PHPVER  :  5.0
* @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
*             Moderator, phpResource (http://groups.yahoo.com/group/phpresource/)
*             URL: http://www.rupom.info  
* @version :  1.0
* Purpose  :  Creating XML File of Hierarchy Structure of A Given Directory */

include_once(__DIR__.DS.'class.array2xml2array.php');

class PathToXml extends CArray2xml2array {
    public  $arr;
    private $mainString;
    private $path;

    public function __construct($path) {
        $this->arr        = [];
        $this->mainString = '';
        $this->path       = $path;
    }

    public function traverseDirFiles($path) {
        $dir = opendir($path);
        while($file = readdir($dir)) {
            if (($file == ".") or ($file == "..")) {
                continue;
            }
            if (filetype("$path/$file") == "dir") {
                $this->traverseDirFiles("$path/$file");
            } else {
                $this->processFiles($path,$file);
            }
        }
        closedir($dir);
    }

    public function processFiles($path,$file) {
        if (trim($path) == trim($this->path)) {
            $arr_to_form = 'this->arr[pathRoot]';
        } else {
            $dat         = $this->formArray($this->path.$path);
            $arr_to_form = 'this->arr[pathRoot]'.$dat;
        }

        $searches = ["[","]"];
        $reps     = ["['","']"];
        $arr_2    = str_replace($searches,$reps,trim($arr_to_form));
        $vr = $arr_2.'[]';
        //assigns $file to array "$arr"
        eval("\$$vr=\"$file\";");
    }

    public function showDirHierarchy() {
        $this->dBug($this->arr);
    }

    function dBug($dump) {
        echo "<pre>";
        print_r($dump);
        echo "</pre>";
    }

    function formArray($str) {
        $arr = explode("$this->path/",$str);
        $arr = explode("/",$arr[1]);
        foreach($arr as $i => $v) {
            $arr_to_form .= "[$v]";
        }
        return $arr_to_form;
    }
}
