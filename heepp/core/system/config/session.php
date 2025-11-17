<?php
/* Set storage engine for session data. At the moment you can choose between 
 * PHP which will use the normal way php stores sessin variables, or you can use
 * the SQL option that will store all the datase in a db table. Remmber to 
 * create table
 */
define('SESSION_STORAGE','PHP');

// Include Session Handler before the session starts
if (SESSION_STORAGE === 'SQL') {
    include 'core'.DS.'extension'.DS.'database'.DS.'Database.php';
    include 'core'.DS.'system'.DS.'handlers'.DS.'SQLSessionHandler.php';
}

// Start session if it does not exist
if (!isset($_SESSION)) {
    $sessionPath = $_ENV['project.path'].'sessions';
    if (!file_exists($sessionPath) && is_writable(dirname($sessionPath))) {
        mkdir($sessionPath,0755,true);
    }
    session_save_path($sessionPath);
    session_start();
}

// Reset current session
if (isset($_GET['reset'])) {
    unset($_SESSION['heepp']);
    unlink(env('project.path').'uiConstants.js');
    header('location: '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']);
}

// Check if the session exists
if (!isset($_SESSION['heepp']->project)) {
    $_SESSION['heepp'] = new stdClass();
    $_SESSION['heepp']->project = env('project.name');
}
