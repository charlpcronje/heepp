<?php
    if (defined('API_URL')) {
        $apiConstants['API_URL'] = API_URL;
    }
    
    if (defined('API_KEY')) {
        $apiConstants['API_KEY'] = API_KEY;
    }
    if (!isset($apiConstants)) {
        $apiConstants = [];
    }

    $projectConstants = [];
    if (defined('WEBSITE_TITLE')) {
        $projectConstants = [
            'WEBSITE_TITLE'            => WEBSITE_TITLE,
            'WEBSITE_MAIN_HEADING'     => WEBSITE_MAIN_HEADING,
            'TIMEZONE'                 => TIMEZONE,
            'LOCALE'                   => LOCALE,
            'AUTHOR_COMPANY'           => AUTHOR_COMPANY,
            'AUTHOR_COMPANY_URL'       => AUTHOR_COMPANY_URL,
            'AUTHOR_COMPANY_TEXT'      => AUTHOR_COMPANY_TEXT,
            'AUTHOR_COMPANY_LINK_TEXT' => AUTHOR_EMAIL_ADDRESS,
            'AUTHOR_CONTACT_NUMBER'    => AUTHOR_CONTACT_NUMBER
        ];
    }

    // UI Settings
    $pathConstants = [
        'PROJECT'                  => env('project.name'),
        'PROJECT_UPLOAD_PATH'      => env('project.upload.url'),
        'PROJECT_PATH'             => env('project.url'),
        'PROJECT_CONTROLLERS_PATH' => env('project.controllers.url'),
        'PROJECT_ASSETS_PATH'      => env('project.assets.url'),
        'BASE_PATH'                => env('base.url'),
        'ELEMENT_PATH'             => env('core.element.url'),
        'LIBRARY_PATH'             => env('core.library.url'),
        'FRAGMENT_PATH'            => env('core.fragment.url'),
        'PROJECT_FRAGMENT_PATH'    => env('project.fragments.url'),
        'HTTP_HOST'                => env('http.host'),
        'REQUEST_URI'              => env('request.uri')
    ];

    $socialConstants = [];
    if (defined('GOOGLE_TRACKING')) {
        $socialConstants = [
            // Google
            'GOOGLE_TRACKING'         => GOOGLE_TRACKING,
            'GOOGLE_SOCIAL_LINK'      => GOOGLE_SOCIAL_LINK,
            // Facebook
            'FACEBOOK_ACCOUNT'        => FACEBOOK_ACCOUNT,
            'FACEBOOK_APP_ID'         => FACEBOOK_APP_ID,
            'FACEBOOK_APP_SECRET'     => FACEBOOK_APP_SECRET,
            'FACEBOOK_SOCIAL_LINK'    => FACEBOOK_SOCIAL_LINK,  

            // Twitter
            'TWITTER_ACCOUNT'         => TWITTER_ACCOUNT,
            'TWITTER_CONSUMER_KEY'    => TWITTER_CONSUMER_KEY,
            'TWITTER_CONSUMER_SECRET' => TWITTER_CONSUMER_SECRET,
            'TWITTER_SOCIAL_LINK'     => TWITTER_SOCIAL_LINK
        ];
    }

    $uiConstants = [
        'ENVIRONMENT' => env('dep.env',null,'dev'),
        'ZIP_HTML'    => env('compress.output',null,false),
        'PATH'        => $projectConstants,
        'PROJECT'     => $pathConstants,
        'SOCIAL'      => $socialConstants,
        'API'         => $apiConstants
    ];
define('UI_CONSTANTS',json_encode($uiConstants));
file_put_contents(env('project.path').'uiConstants.js','const UI_CONSTANTS = '.json_encode($uiConstants));