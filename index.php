<?php
if (isset($_SERVER['HTTP_REFERER']) && !defined('HTTP_REFERER')) {
    define('HTTP_REFERER',$_SERVER['HTTP_REFERER']);
}
define('ROOT_PATH',dirname(__DIR__.DIRECTORY_SEPARATOR));

/* Setting the Base Path for HeEPP. Use to set in the calling project.
in the $basePath variable so if this stops working refer to the value of: */
$basePath ?? '/core/app/';

putenv("base.path=".__DIR__);

// Bootstrap autoloads only the files for HeEPP to Instantiate and the auto loads any other classes as they are requested.
include 'core/system/bootstrap.php';

// As soon as bootstrap.php is done all the functionality of Heepp is available.
use core\extension\helper\Asset;
use core\Heepp;
use core\system\handlers\ProjectLoader;
use core\system\route;

if (route::exists()) {
    route::invoke(route::getRouteDetails());
} else {
    if (Asset::isAsset(input('controller'),input('params'))) {
        ProjectLoader::loadAsset(input('controller'),input('params'));
    } elseif (input('controller') === 'uiConstants.js') {
        ProjectLoader::loadJSConstants();
    } elseif (Heepp::data('app.request.allowDirectRouting')) {
        // Check if controller URL Param is set
        if (Heepp::data('app.request.route') === '/') {
            header('Content-type:text/html; charset=utf-8');

            // Reset loaded libraries
            unset($_SESSION['heepp']->libraries);
            $_SESSION['heepp']->libraries = new stdClass();

            // If the controller is NOT set then load the project
            ProjectLoader::loadProject();
        } else {
            if (env('compress.output',null,0)) {
                header('Content-type: text/html; charset=utf-8');
            } else {
                header('Content-Type: application/json');
            }

            // If the controller IS set then load the controller
            ProjectLoader::loadController();
        }
    } else {
        route::load('404','get','core');
    }
}
