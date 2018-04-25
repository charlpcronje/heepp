<?php
define('HTTP_REFERER',$_SERVER['HTTP_REFERER']) ;
define('ROOT_PATH',dirname(__DIR__.DIRECTORY_SEPARATOR));

// Bootstrap will load the minimum files fore the Heepp to work.
include 'core/system/bootstrap.php';

// As soon as bootstrap.php is done all the functionality of Heepp is available.
use core\Heepp;
use core\system\handlers\ProjectLoader;
use core\system\route;

if (route::exists()) {
    route::invoke(route::getRouteDetails());
} else {
    if (input('controller') === 'assets' || input('controller') === 'views' || input('controller') === 'uiConstants.js') {
        ProjectLoader::loadAsset();
    } elseif(input('controller') === 'uiConstants.js') {
        ProjectLoader::loadJSConstants();
    } elseif (Heepp::data('app.request.allowDirectRouting')) {
        // Check if controller URL Param is set
        if (Heepp::data('app.request.route') === '/') {
            header('Content-type:text/html; charset=utf-8');

            // Reset loaded libraries
            unset($_SESSION['core']->libraries);
            $_SESSION['core']->libraries = new stdClass();

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
