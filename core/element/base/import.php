<?php
namespace core\element\base;
use core\Element;
use core\extension\cache\cache;
use core\extension\ui\view;

class import extends Element {
    public $src;
    public $path;
    public $cache;

    public function render() {
        $this->path = env('project.path');
        $removeSrcAttrPresets = str_replace(env('document.root'),'',$this->path);
        $this->src = str_replace($removeSrcAttrPresets,'',$this->src);
        if (isset($this->cache)) {
            if (cache::exists($this->cache)) {
                return cache::get($this->cache);
            }
            $html = view::mold($this->src,$this->path);
            cache::set($this->cache,$html);
            return $html;
        }
        return view::mold($this->src,$this->path);
    }
}
