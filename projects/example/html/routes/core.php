<?php
use core\extension\ui\view;
use core\system\route;
route::get('404',function() {
    http_response_code(404);
    return view::mold('404.phtml',env('core.app.views.path'));
});

route::get('/console',function() {
    return view::phtml('index.phtml',env('core.app.views.path'));
});

route::get('/user/login','User@login');
