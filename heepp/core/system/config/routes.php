<?php
use core\Heepp;
use core\system\route;

if (inputSet('controller')) {
    if (strpos(input('controller'),'@') !== false) {
        $params = explode('@',input('controller'));
        // The App Type Prefix Define the Routes file that will be run (web by default)
        env('app.type.prefix',$params[0]);
        $route = $params[1];
    } else {
        $route = input('controller');
    }
    if (inputSet('params')) {
        $route .= input('params');
    }
} else {
    $route = 'root';
}

$requestMethod = strtolower(env('request.method',null,'get'));
if (!empty(env('app.type.prefix'))) {
    route::$requestRoute = 'app.request.route.'.$requestMethod.':'.env('app.type.prefix').':'.str_replace('/',':',$route);
} else {
    route::$requestRoute = 'app.request.route.'.$requestMethod.':'.str_replace('/',':',$route);
}

if (Heepp::dataKeyExists('app.request.types')) {
    foreach(Heepp::data('app.request.types') as $type => $options) {
        if (file_exists($options->file) && $options->prefix === env('app.type.prefix')) {
            route::appType($type);
            include $options->file;
        }
    }
}
