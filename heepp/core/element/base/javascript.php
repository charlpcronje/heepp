<?php
namespace core\element\base;
use core\Element;

class javascript extends Element {
    public $src;
    public $path;
    public $async;
    public $defer;
    public $charset = 'UTF-8';
    public $lazyload = false;

    private function jscript() {
        $this->setElement('script');
    }

    public function render() {
        // Just easier to remember
        if ($this->lazyload) {
            $this->defer = 'defer';
        }

        if ($this->src != null && strpos($this->src,'/app') === 0) {
            $this->src = str_replace(env('project.url'),env('core.url'),$this->src);
        }
        $this->jscript();

        // basically if this attribute is set in any way it will be loaded in async
        if (isset($this->async)) {
            $this->addAttr('async','async');
        }

        // basically if this attribute is set in any way it will be "lazy loaded"
        if (isset($this->defer)) {
            $this->addAttr('defer','defer');
        }

        if (isset($this->charset)) {
            $this->addAttr('charset',$this->charset);
        }
        return parent::render();
    }
}
