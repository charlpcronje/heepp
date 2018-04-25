<?php
namespace core\element\base;
use core\Element;

class javascript extends Element {
    public $src;
    public $path;

    private function jscript() {
        $this->setElement('script');
    }
    
    public function render() {
        if (strpos($this->src,'/app') === 0) {
            $this->src = str_replace(env('project.url'),env('core.url'),$this->src);
        }
        $this->jscript();
        return parent::render();
    }
}
