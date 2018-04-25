<?php
namespace core\element\base;
use core\extension\ui\less\lessc\lessc;
use core\extension\ui\scss\Compiler;
use core\extension\ui\view;
use core\Element;

class stylesheet extends Element {
    public $src;
    public $recompile = false;
    public $href;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    private function sheet() {
        $hrefSet = false;
        if (strpos($this->src, '.less') !== false) {
            $lessFile = $this->src;
            $newSrc   = str_replace('.less','.css',$this->src);
            $less     = new lessc;
            $less->checkedCompile($lessFile,$newSrc);
        } elseif(strpos($this->src, '.scss') !== false) {
            $scssFile = $this->src;
            $appPath = str_replace(env('project.url'),'',$scssFile);
            if (strpos($this->src,'/app') !== false) {
                if (file_exists($this->src)) {
                    $scssFile  = $this->src;
                    $this->src = str_replace([env('core.path'),'/'],[env('core.url'),DS],$this->src);
                    $this->href = str_replace('.scss','.css',$this->src);
                    $hrefSet = true;
                } else {
                    $scssFile = str_replace([env('project.url'),'\\\\'],[env('core.url'),'\\'],$this->src);
                    $baseSrc  = str_replace([env('core.path'),'/'],[env('core.url'),DS],$this->src);
                }
            } elseif (strpos($this->src,env('request.scheme')) !== 0) {
                $this->src = str_replace('/',DS,$this->src);
                $scssFile = env('project.path').$this->src;
                $this->src = env('project.url').$this->src;
                $this->href = str_replace('.scss','.css',$this->src);
                $hrefSet = true;
            } else {
                $baseSrc = str_replace([env('project.url'),'/'],[env('project.path'),DS],$this->src);
                // $baseSrc = str_replace([env('project.path'),DS],[env('project.url'),'/'],$this->src);
                $this->href = str_replace('.scss','.css',$this->src);
                $hrefSet = true;
                $scssFile = $baseSrc;
            }

            $fullPath = $scssFile;
            $baseName = basename($fullPath);
            $path     = str_replace($baseName,'',$fullPath);
            $newSrc   = str_replace('.scss','.css',$fullPath);
            $recompile = $this->recompile;
            if (!file_exists($newSrc) || is_dir($newSrc)){
                $recompile = true;
            } elseif((file_exists($newSrc) && filemtime($fullPath) > filemtime($newSrc)) || env('cache.scss',null,1) == 0) {
                $recompile = true;
            }
            if ($recompile) {
                $scss = new Compiler();
                $scss->setImportPaths($path);
                $css = $scss->compile('@import "'.$baseName.'"');
                file_put_contents($newSrc,$css);
            }
            if (!$hrefSet) {
                $this->href = $newSrc;
            }
        } else {
            $this->href = $this->src;
        }
    }

    public function render() {
        $this->sheet();
        return view::mold('stylesheet.phtml',__DIR__,$this);
    }
}
