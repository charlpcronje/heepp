<?php
namespace core\element\system;

class cpuusage extends \core\Element {
    function __construct($element = null) {
        $this->element = __class__;
        parent::__construct($element);
    }

    function render() {
        $os=strtolower(PHP_OS);
        if(strpos($os, 'win') === false){
            if(file_exists('/proc/loadavg')){
                $load = file_get_contents('/proc/loadavg');
                $load = explode(' ', $load, 1);
                $load = $load[0];
            } elseif(function_exists('shell_exec')){
                $load = explode(' ', `uptime`);
                $load = $load[count($load)-1];
            } else {
                return false;
            }

            if(function_exists('shell_exec')) {
                $cpuCount = shell_exec('cat /proc/cpuinfo | grep processor | wc -l');
            }
            return array('load'=>$load, 'procs'=>$cpuCount);
        } else {
            if(class_exists('\COM')) {
                $wmi=new COM('WinMgmts:\\\\.');
                $cpus=$wmi->InstancesOf('Win32_Processor');
                $load=0;
                $cpuCount=0;
                if(version_compare('4.50.0', PHP_VERSION) == 1) {
                    while($cpu = $cpus->Next()){
                        $load += $cpu->LoadPercentage;
                        $cpuCount++;
                    }
                } else {
                    foreach($cpus as $cpu) {
                        $load += $cpu->LoadPercentage;
                        $cpuCount++;
                    }
                }
                return array('load'=>$load, 'procs'=>$cpuCount);
            }
            return 'Unknown';
        }
        return false;
    }
}
