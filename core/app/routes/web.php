<?php
use core\Heepp;
use core\system\route;

/* Website Content Pages
|----------------------- */
route::get('/login','User@login');
route::get('/','Console@index');
route::get('/old','Console@old');


// File Browser
route::get('/filebrowser/action/?{action}/?{id}/?{textOrParent}/?{type}',function() {
    (new \core\element\ui\filebrowser\filebrowser())->action(Heepp::data('app.request.data.action'),Heepp::data('app.request.data.id'),Heepp::data('app.request.data.textOrParent'),Heepp::data('app.request.data.type'));
});

route::group([
    'prefix' => 'member',
    'before' => function() {
        if (!Heepp::dataKeyExists('session.user.id')) {
            $prefix = Heepp::data('app.request.options.routePrefix');
            $route = Heepp::data('app.request.options.route');
            if (!empty($prefix)) {
                $route = $prefix.'/'.$route;
            }
            $route = '/'.$route;
            Heepp::data('session.login.after.route',$route);
            (new Heepp)->redirect('/member-login');
        }
    }
],function() {
    route::get('personal-details','Member@personalDetails');
    route::get('sign-out',function() {
        Heepp::forgetKey('session.user');
        (new Heepp)->redirect('/');
    });
});
