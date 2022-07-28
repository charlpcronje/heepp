<?php

/**
 * @author Charl Cronje <charlcp@gmail.com>
 * @date 01 Dec 2015
 * @time 1:59:24 AM
 */
namespace core\extension\helper;

class Session extends \core\extension\Extension {
    function __construct() {
        parent::__construct();
        
        // Start the session of it does not exist
        if (!isset($_SESSION)) {
            session_start();
        }
    }
}
