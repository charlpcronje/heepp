<?php
ini_set('allow_url_fopen',1);

// Ignores the user's attempt to stop te script
ignore_user_abort(true);

// Set Max Execution Time (0 = no time limit)
set_time_limit(0);

//E_ERROR | E_WARNING | E_PARSE | E_NOTICE
ini_set('display_errors',E_ALL);

// Report all errors
error_reporting(E_ALL);

// Same as error_reporting(E_ERROR);
ini_set('error_reporting',E_ALL);
ini_set('date.timezone', 'Africa/Johannesburg');

// Check for Set URI Params
if (isset($_GET['q'])) {
    $_SERVER['REQUEST_URI'] = str_replace($_GET['q'],'',env('request.uri'));
}
if (isset($_GET['params'])) {
    $_SERVER['REQUEST_URI'] = str_replace($_GET['params'],'',env('request.uri'));
}

//Declare variable to that they exist in the model (Undefined Index happens)
$_SERVER['CALLED_MODULE'] = '';
$_SERVER['CALLED_METHOD'] = '';
$_SERVER['CALLED_PARAMS'] = '';
