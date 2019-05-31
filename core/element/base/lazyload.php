<?php
namespace core\element\base;
use core\extension\ui\less\lessc\lessc;
use core\extension\ui\scss\Compiler;
use core\extension\ui\view;

class lazyload extends \core\Element {
    public $src;
    public $file;
    public $path;
    public $url;
    public $fileInfo;
    
    // The js callback function to execute when the file is done loading
    // The callback will not be executed of a $this->instantiate is set
    public $callback;
    
    // This is the js object this will be passed to the callback function
    public $param;
    
    // The js context (js object) in which the callback will execute in
    public $context;
    
    // The name of the js class in the file being included that will be instantiated
    // automatically after the file is loaded
    public $instantiate;
    public $extend = 'app';

    // absolute path
    public $absPath;
    public $type;
    
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
        $this->fileInfo = new \stdClass();
    }

    private function getProperties() {
        // Check if a path and url and file was set
        if (isset($this->path) && isset($this->url) && isset($this->file)) {
            $this->fileInfo = (object)pathinfo($this->path.$this->file);
            if (isset($this->fileInfo->query)) {
                if (strpos($this->fileInfo->extension,$this->fileInfo->query) !== false) {
                    $this->fileInfo->extension = str_replace([$this->fileInfo->query,'?'],'',$this->fileInfo->extension);
                }
            }
        } else
        // Check if the letters 'http' does not exist at offset pos 0 in $this->src
        if (isset($this->src) && strpos(strtolower($this->src),HTTP,0) === false) {
            $this->absPath = true;
            $this->src = BASE_PATH.str_replace('/',DS,$this->src);
            
            /* 
             * 'http' is not in the beginning of the string
             *  pathinfo return the following: [
             *      [dirname] => C:\xampp\htdocs\core\projects\constantia\views\shop\js
             *      [basename] => index.js
             *      [extension] => js
             *      [filename] => index
             *  ]
             */
            
            $this->fileInfo = (object)pathinfo($this->src);
            if (isset($this->fileInfo->query)) {
                if (strpos($this->fileInfo->extension,$this->fileInfo->query) !== false) {
                    $this->fileInfo->extension = str_replace('?','',str_replace($this->fileInfo->query,'',$this->fileInfo->extension));
                }
            }
            $this->src = url($this->src);
        } else {
            $this->absPath = false;
            /* 
             * 'http' is in the beginning of the string
             *  parse_url return the following: [
             *      'scheme' => https
             *      'host'   => ajax.googleapis.com
             *      'path'   => /ajax/libs/jquery/3.2.1/jquery.min.js
             *  ]
             */
            
            $this->fileInfo = (object)array_merge(pathinfo($this->src),parse_url($this->src));
            if (isset($this->fileInfo->query)) {
                if (isset($this->fileInfo) && isset($this->fileInfo->extension) && strpos($this->fileInfo->extension,$this->fileInfo->query) !== false) {
                    $this->fileInfo->extension = str_replace('?','',str_replace($this->fileInfo->query,'',$this->fileInfo->extension));
                }
            }
        }

        /* Some 3rd party js scripts does not have an extension, for example the
         * google maps API: https://maps.googleapis.com/maps/api/js?key=,
         * In this case i'm assuming it is of type 'js'
         */
        if (!isset($this->fileInfo->extension)) {
            $this->fileInfo->extension = 'js';
        }
        
        switch(strtolower($this->fileInfo->extension)) {
            case 'css':
                $this->type = 'css';
            break;
        
            // When less is found as the type I must parse the less to css and the browser css file.
            case 'less':
                $this->type = 'css';
                if (isset($this->src)) {
                    $lessFile = $this->src;
                    $this->src = str_replace($this->fileInfo->extension,'css',$this->src);
                    $less = new lessc;
                    $less->checkedCompile($lessFile,$this->src);
                } elseif (isset($this->path) && isset($this->file)) {
                    $lessFile = $this->path.$this->file;
                    $less = new lessc;
                    $less->checkedCompile($lessFile,$this->url.$this->file);
                }
            break;
            case 'scss':
                $this->type = 'css';
                if (isset($this->src)) {
                    $scssFile = $this->src;
                    if (strpos($scssFile,'http://') !== false) {
                        $scssFile = str_replace('core/projects/','',$scssFile);
                        $scssFile = str_replace('/',DS,str_replace(env('base.url'),env('project.path'),$scssFile));
                        $fullPath = $scssFile;
                    } else {
                        $fullPath = BASE_PATH.$scssFile;
                    }
                } elseif (isset($this->path) && isset($this->file)) {
                    $scssFile = $this->path.$this->file;
                    $fullPath = $scssFile;
                }
                $baseName = basename($fullPath);
                $path = str_replace($baseName,'',$fullPath);
                $newSrc = str_replace('.scss','.css',$fullPath);
                $recompile = false;
                if (!file_exists($newSrc) || is_dir($newSrc)){
                    $recompile = true;
                } elseif(file_exists($newSrc) && filemtime($fullPath) > filemtime($newSrc)) {
                    $recompile = true;
                }

                if ($recompile) {
                    $scss = new Compiler();
                    $scss->setImportPaths($path);
                    $css = $scss->compile('@import "'.$baseName.'"');
                    file_put_contents($newSrc,$css);
                }
                if (isset($this->src)) {
                    $this->src = str_replace('\\','/',str_replace(env('project.path'),env('project.url'),$newSrc));
                } elseif (isset($this->path) && isset($this->file)) {
                    $this->src = $this->url.basename($newSrc);
                }
            break;
            case 'js':
                $this->type = 'js';
            break;
        }
    }

    public function render() {
        $uniqueId = uniqid('script_');
        $this->getProperties();
        
        $script = "<script id=\"$uniqueId\">\n";
        $script .= 'LazyLoad.'.$this->type.'(\''.$this->src.'\',function('.(isset($this->param) ? 'arg' : '').") { \n";
        if ($this->instantiate) {
            $script .= '     core.'.$this->extend.'.'.$this->instantiate.' = new '.$this->instantiate.'('.(isset($this->param) ? 'arg' : '')."); \n";
        } 
 
        if (isset($this->callback)) {
            $script .= '    '.$this->callback." \n";
            if (substr($this->callback,-1,1) != ')') {
               $script .= '('.(isset($this->param) ? 'arg' : '')."); \n";
            }
        }
        $script .= '},'.(isset($this->param) ? $this->param : 'null').''.(isset($this->context) ? ','.$this->context : '')."); \n"; 
        $script .= "var child = document.getElementById('$uniqueId'); \n";
        $script .= "child.parentNode.removeChild(child); \n";
        $script .= '</script>';
        
        return $script;
    }
}
