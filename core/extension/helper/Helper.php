<?php
namespace core\extension\helper;

class Helper extends \core\extension\Extension {
    function __construct() {
        parent::__construct();
        if (empty($_GET['params'])) {
            $this->renderCore();
        }
    }

    function renderCore() {
        header('Content-Type: text/html; charset=utf-8');
        $fo = new coreFO();
        $fo->loadCoreXML('index.xml',$this);
        echo $fo->html;
    }

    function openDashboard() {
        $fo = new coreFO();
        $fo->loadCoreXML('introduction/openIntroduction.xml',$this);
        $this->setHtml('#wcontent',$fo->html);
        $this->setBreadCrumb('Dashboard');
    }

    function openXML($xmlFile,$breadcrumb = null) {
        $fo = new CoreUI();
        $fo->loadCoreXML($xmlFile,$this);
        $this->setHtml('#wcontent',$fo->html);
        $this->setBreadCrumb($breadcrumb,'Helpers/openXML/'.$xmlFile);
    }
}
