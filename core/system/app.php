<?php
namespace core\system;
use core\Heepp;
use core\system\data;
use core\system\env;

class app extends Heepp {
    public static $appSettings  = [];
    private static $configPaths = [];

    public function __construct() {
        parent::__construct(__CLASS__);

        self::$configPaths = [
            'system'  => realpath(env('system.app.path')).DS,
            //'core'    => realpath(env('core.app.config.path')),
            'project' => realpath(env('project.config.path')).DS
        ];
    }

    public static function init() {
        return (new app())->config();
    }

    private function config() {
        foreach(self::$configPaths as $path) {
            self::loadConfig($path);
        }
        return $this;
    }

    public static function loadConfig($path = null) {
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            $filename = $fileInfo->getFilename();
            $extension = $fileInfo->getExtension();
            if ($extension === 'php') {
                $propertykey = str_replace($extension,'',$filename);
                if (!file_exists($path.$filename)) {
                    continue;
                }
                $dataValues = include $path.$filename;
                if (is_array($dataValues)) {
                    foreach($dataValues as $dotName => $value) {
                        Heepp::data($propertykey.$dotName,$value);
                    }
                }
            }

        }
        // self::$appConfig = array_replace_recursive(self::$appConfig,$projectConfig);
        // foreach($appConfig as $key => $setting) {
        //     Heepp::data('app.'.$key,$setting);
        // }
    }

}
