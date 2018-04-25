<?php

// Super Globals
if (isset ($_SERVER['HTTP_REFERER'])) {
    define('HTTP_REFERER',$_SERVER['HTTP_REFERER']) ;
}

// PHP Version
if (!defined(PHP_VERSION)) {
    /** @noinspection ConstantCanBeUsedInspection */
    define('PHPVERSION',(int)phpversion());
}

// Check SSL certificate | $_SERVER['HTTPS'] = 'off' on IIS
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') { 
    define('SSL',true);
    define('HTTP','https');
} else { 
    define('SSL',false);
    define('HTTP','http');
}

// Get list of all projects
$projectList = [];
foreach((array)env('projects') as $project => $settings) {
    $projectList[$project] = $settings->path;
}
define('PROJECT_LIST',serialize($projectList));

// Date
define('NOW',date ('Y-m-d H:i:s'));
define('CURRENT_DATE',date ('Y-m-d'));
define('CURRENT_YEAR',date ('Y-m-d'));
define('CURRENT_YEAR_ONLY',date('Y'));
define('PREVIOUS_YEAR',date ('Y-m-d', strtotime('-1 years')));
define('NEXT_YEAR',date ('Y-m-d', strtotime('+1 years')));
define('CURRENT_MONTH',date ('Y-m-d'));
define('PREVIOUS_MONTH',date ('Y-m-d',strtotime('-1 months')));
define('NEXT_MONTH',date ('Y-m-d', strtotime('+1 months')));
define('CURRENT_TIME',date('H:i:s'));
define('CURRENT_TIMESTAMP',date('Y-m-d H:i:s'));
define('CURRENT_UNIX_TIMESTAMP',strtotime(date('Y-m-d H:i:s')));