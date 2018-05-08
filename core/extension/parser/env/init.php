<?php
require_once __DIR__.DS.'exception'.DS.'ExceptionInterface.php';
require_once __DIR__.DS.'exception'.DS.'InvalidPathException.php';
require_once __DIR__.DS.'exception'.DS.'InvalidFileException.php';
require_once __DIR__.DS.'exception'.DS.'ValidationException.php';
require_once __DIR__.DS.'exception'.DS.'InvalidCallbackException.php';
require_once __DIR__.DS.'Loader.php';
require_once __DIR__.DS.'DotEnv.php';

use core\extension\parser\env\DotEnv;

$origin = realpath(dirname(fileIncludeOrigin()));
if ($origin != realpath('./')) {
    (new DotEnv($origin))->load();
}
(new DotEnv(realpath('./')))->load();
