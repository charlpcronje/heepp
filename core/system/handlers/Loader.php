<?php
namespace core\system\handlers;
use core\Heepp;
use core\Output;
use core\system\route;

class Loader extends Heepp{
    public static $includedFiles   = [];
    public static $coreLastLogId;
    public static $sessionExists   = false;
    public static $watchNamespace  = 'heepp';
    public static $callingClass;
    public static $locationsToLook = [
        'nameSpaced'        => 'includeByNamespace',
        'projectController' => 'includeProjectController'
    ];

    protected static function sessionExists() {
        // _DISABLED = 0, // _NONE = 1, // _ACTIVE = 2
        return session_status() == 2;
    }

}
