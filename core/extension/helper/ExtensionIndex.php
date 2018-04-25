<?php
namespace core\extension\helper;

use core\extension\Extension;

class ExtensionIndex extends Extension {
    function __construct() {
        parent::__construct(__CLASS__);
    }

    public function getExtensionNamespaces() {
        return [];
    }

    public function getExtensionsByNS($namespace = '\\core\\extension') {
        return [];
    }
}
