<?php
use core\extension\ui\view;
return [
    'file' => function() {
        return view::phtml('views/layout/header/tools/file');
    },
    'edit' => function() {
        return view::phtml('views/layout/header/tools/edit');
    },
    'view' => function() {
        return view::phtml('views/layout/header/tools/view');
    }
];
