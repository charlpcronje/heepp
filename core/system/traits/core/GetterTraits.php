<?php
namespace core\system\traits\core;
trait GetterTraits  {
    public function getControllers() {
        $xml = loadXML(env('project.controllers.path').'controllers.xml',LIBXML_NOCDATA);
        $controllers = array();
        $i = 0;
        foreach($xml->children() as $child) {
            $controllers[$i] = array('name' =>$child->getName(),'alias' => (string)$child['alias'],'icon' => (string)$child['icon'],'status' => (string)$child['status']);
            if (is_array($child->functions->children())) {
                foreach($child->functions->children() as $function) {
                    $controllers[$i]['functions'][] = array('name' =>$function->getName(),'alias' => (string)$function['alias'],'status' => (string)$function['status']);
                }
            }
            $i++;
        }
        return $controllers;
    }

    public function getControllerFunctions($controller) {
        $xml = loadXML(env('project.controllers.path').'controllers.xml',LIBXML_NOCDATA);
        $functions = array();
        if (@count($xml->$controller->functions->children()) > 0) {
            foreach($xml->$controller->functions->children() as $child) {
                $functions[] = array('name' =>$child->getName(),'alias' => (string)$child['alias'],'status' => (string)$child['status']);
            }
        }
        return $functions;
    }
}
