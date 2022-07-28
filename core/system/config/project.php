<?php
// define('FORCE_PROJECT','heepp');

// Define local IP addresses in [], serializing because php < 7 don't support arrays in a constant,
// Remember to un-serialize when using as an array
define('LOCAL_IP_ADDRESSES',serialize([
    '127.0.0.1'
]));

// Define current project
if (!empty($_GET['project'])) {
    define('PROJECT',$_GET['project']);
} elseif(isset($_GET['controller']) && strpos($_GET['controller'],'=') !== false) {
    $projectAndController = explode('=',$_GET['controller']);
    define('PROJECT',$projectAndController[0]);
    $_GET['controller'] = $projectAndController[1];
} elseif(defined('FORCE_PROJECT')) {
    define('PROJECT',FORCE_PROJECT);
} else {
    /* If core is running on your localhost it checks for localhost/projectName. There are some exploits that
     * be run on $_SERVER['HTTP_HOST']. Like when sending specific header info and then changing the HTTP_HOST in that way.
     * So to make it saver specify a list of allowed domains so that this exploit can't be used.
     * @noinspection HostnameSubstitutionInspection */
    $hostArg = explode('.',env('http.host'));
    if (array_shift($hostArg) === 'localhost' || in_array(env('http.host'),unserialize(LOCAL_IP_ADDRESSES))) {
        $requestURI = explode('/',env('request.uri'));
        define('PROJECT',$requestURI[1]);
    } else {
        // If base is running from a webServer with a sub-domain the sub-domain is used for the project name
        $project = explode('.',env('http.host'));
        define('PROJECT',array_shift($project));
    }
}
