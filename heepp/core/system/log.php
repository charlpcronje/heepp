<?php
namespace core\system;
use core\Heepp;

class log {
    public static function success($log,$class = null) {
        return (new Heepp)->log('success',$log,$class);
    }

    public static function notice($log,$class = null) {
        return (new Heepp)->log('notice',$log,$class);
    }

    public static function warning($log,$class = null) {
        return (new Heepp)->log('warning',$log,$class);
    }

    public static function danger($log,$class = null) {
        return (new Heepp)->log('danger',$log,$class);
    }

    public static function error($log,$class = null) {
        return (new Heepp)->log('error',$log,$class);
    }

    public static function info($log,$class = null) {
        return (new Heepp)->log('info',$log,$class);
    }
}
