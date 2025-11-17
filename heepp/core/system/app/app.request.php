<?php
return [
    /* Routes to Parse
	|--------------- */
    /* If allowDirect = true: no routes are required, a  Controller with method can be called
     * by formulating a request like: Controller/Method/Argument1/Argument2... Example
     * User/getUserDetailsById/1. This will call Controller: User, Method: getUserDetailsById, Argument1: 1*/
    'allowDirectRouting'       => true,

    /* Heepp Routes
    |------------- */
    'types.core.file'         => env('core.app.routes.path').'core.php',
    'types.core.prefix'       => 'core',
    'types.core.before'       => [],
    'types.core.after'        => [],
    'types.core.allowedVerbs' => [
        'post',
        'get',
        'put',
        'patch',
        'delete',
        'options'
    ],
    'types.core.defaultVerb' => 'get',
    'types.core.contentType' => env('app.header.type'),

    /* Web Routes
    |------------ */
    'types.web.file'         => env('project.routes.path').'web.php',
    'types.web.prefix'       => '',
    'types.web.before'       => [],
    'types.web.after'        => [],
    'types.web.allowedVerbs' => [
        'post',
        'get',
        'options'
    ],
    'types.web.defaultVerb'  => 'get',
    'types.web.contentType'  => env('app.header.type'),

    /* API Routes
    |------------ */
    'types.api.file'         => env('project.routes.path').'api.php',
    'types.api.prefix'       => 'api',
    'types.api.before'       => [],
    'types.api.after'        => [],
    'types.api.allowedVerbs' => [
        'post',
        'get',
        'put',
        'patch',
        'delete',
        'options'
    ],
    'types.api.defaultVerb'  => 'post',
    'types.api.contentType'  => env('json.header'),

    /* Request Default
    |----------------- */
    'defaultType'            => 'web'
];
