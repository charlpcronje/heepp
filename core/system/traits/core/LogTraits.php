<?php
namespace core\system\traits\core;
use core\extension\database\Database;

trait LogTraits {
    public function logBegin($function,$params = null) {
        if ($this->session('user.id') !== null) {
            $db = new Database();
            /** @noinspection SqlResolve */
            $sql = 'INSERT INTO `log` (
                        `user_id`,
                        `function`,
                        `params`
                    ) VALUES (
                        '.$this->session('user')['id'].",
                        '".$db->escape_value($function)."',
                        '".$db->escape_value(json_encode($params))."'
                    )
                    ";
            $db->query($sql);
            global $coreLastLogId;
            $coreLastLogId = $db->insert_id();
        }
    }

    public function log($type, $log = null, $class = null) {
        $logTypes = ['success','notice','warning','danger','error','info'];
        $logmd = '';
        /*No type needs to be specified. If the type is not one of the $logTypes
         * then I accept that the $type is meant to be the log.
         * The @param $class (optional) can be supplied to create log for a specific class
         * a separate log file wil be created that has the {name of class}.log
        */
        if (!in_array($type,$logTypes)) {
            $logmd = "\r\n# ".date('Y-m-d H:i:s')." INFO \r\n ".$type." \r\n";
        } elseif(isset($log)) {
            $logmd = "\r\n# ".date('Y-m-d H:i:s').' '.strtoupper($type);
            if(isset($class)) {
                $logmd .= '. CLASS: '.$class." \r\n";
            } else {
                $logmd .= " \r\n";
            }
        }

        if (is_array($log)) {
            foreach($log as $key => $value) {
                if (is_string($value)) {
                    $string = $value;
                } elseif (is_array($value) || is_object($value)) {
                    $string = "\r\n``` \r\n".json_encode($value,JSON_PRETTY_PRINT)." \r\n ``` \r\n";
                }
                if($key == 'html' || $key == 'json') {
                    $logmd .= '### '.$key." \r\n";
                    $logmd .= "```\r\n";
                    $logmd .= $string." \r\n";
                    $logmd .= "```\r\n";
                } else {
                    $logmd .= '### '.$key." \r\n";
                    $logmd .= $string."\r\n";
                }
            }
        } else {
            $logmd .= $log;
        }

        if (!isset($class)) {
            $file = 'core-'.date('Y-m-d').'.log.md';
        } else {
            $file = $class.'-'.date('Y-m-d').'.log.md';
        }


        // Create the project log folder if it does not exists
        if (!file_exists(env('project.log.path'))) {
            /** @noinspection MkdirRaceConditionInspection */
            mkdir(env('project.log.path'));
        }
        // Create and write to log if it does 
        if (!file_exists(env('project.log.path').$file)) {
            file_put_contents(env('project.log.path').$file, $logmd);
        } else {
            file_put_contents(env('project.log.path').$file, $logmd, FILE_APPEND);
        }
    }
}
