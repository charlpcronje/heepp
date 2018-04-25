<?php
namespace core\system\handlers;

class CoreLoader extends Loader {
    private static function includeByNamespace($include) {
        $include = str_replace('\\',DS,$include);
        if (file_exists(BASE_PATH.$include.'.php')) {
            include BASE_PATH.$include.'.php';
            return true;
        }
        return false;
    }

    private static function includeProjectController($include) {
        $fileName = $include.'.php';
        if (file_exists(env('project.controllers.path').$fileName)) {
            include env('project.controllers.path').$fileName;
        }
    }
    
    public static function coreAutoLoader($include) {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH',getcwd().DS);
        }
        $exp = explode('\\',$include);
        self::$watchNamespace = null;
        self::$sessionExists = self::sessionExists();

        // Check if file is already included
        if (!in_array($include,self::$includedFiles)) {
            self::$includedFiles[] = $include;
            // Include class or trait
            foreach(self::$locationsToLook as $location => $method) {
                if (self::$method($include)) {
                    return true;
                }
            }
            return false;
        }
    }
}

/* Define Auto Loader */
spl_autoload_register([CoreLoader::class,'coreAutoLoader']);
