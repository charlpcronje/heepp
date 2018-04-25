<?php
namespace core\system;
use core\Heepp;

define('DS',DIRECTORY_SEPARATOR);

/* Helpers
|--------- */
include 'core'.DS.'system'.DS.'helpers'.DS.'general.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'strings.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'math.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'storage.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'request.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'datetime.php';
include 'core'.DS.'system'.DS.'helpers'.DS.'html.php';

/* Load .env before the project
|------------------------------ */
include 'core'.DS.'extension'.DS.'parser'.DS.'env'.DS.'init.php';

/* Session Settings
|------------------ */
include 'core'.DS.'system'.DS.'config'.DS.'session.php';

/* Heepp - Most Other Classes Extends Heepp
|------------------------- */
include 'core'.DS.'Output.php';
include 'core'.DS.'system'.DS.'traits'.DS.'core.php';
include 'core'.DS.'Heepp.php';

/* Auto-Loader
|------------------ */
include 'core'.DS.'system'.DS.'handlers'.DS.'Loader.php';
include 'core'.DS.'system'.DS.'handlers'.DS.'CoreLoader.php';

/* System,Project and Path Constants
|----------------------------------- */
include 'core'.DS.'system'.DS.'config'.DS.'constants.php';
include 'core'.DS.'system'.DS.'config'.DS.'project.php';
include 'core'.DS.'system'.DS.'config'.DS.'paths.php';

/* Loader Handler
|---------------- */
include 'core'.DS.'system'.DS.'handlers'.DS.'ProjectLoader.php';

/* Server Settings
|----------------- */
session('constants',(object)get_defined_constants(true)['user']);
include 'core'.DS.'system'.DS.'config'.DS.'server.php';

/* Startup Handler
|----------------- */
include 'core'.DS.'system'.DS.'handlers'.DS.'StartupHandler.php';

/* Exception Handler
|------------------- */
include 'core'.DS.'system'.DS.'handlers'.DS.'ExceptionHandler.php';

/* Shutdown Handler
|------------------ */
include 'core'.DS.'system'.DS.'handlers'.DS.'ShutdownHandler.php';

/* Helper Classes with static init
|--------------------------------- */
include env('system.path').'data.php';
include env('system.path').'log.php';
include env('system.path').'api.php';

/* Set UI_CONSTANTS for frontend
|------------------------------- */
include $_SERVER['system.config.path'].'uiConstants.php';

/* App Cache
|----------- */
app::init();

/* Database Connections
|---------------------- */
if (Heepp::dataKeyExists('app.db_include."'.env('domain').'"')) {
    include env('project.config.path').'database'.DS.Heepp::data('app.db_include."'.env('domain').'"');
} elseif(Heepp::dataKeyExists('app.db_include') && Heepp::dataKeyExists('app.db_include.default')) {
    include env('project.config.path').'database'.DS.Heepp::data('app.db_include.default');
} elseif(file_exists(env('project.config.path').'database'.DS.'database.php')) {
    include env('project.config.path').'database'.DS.'database.php';
}

/* Routes
|-------- */
include env('system.config.path').'routes.php';
