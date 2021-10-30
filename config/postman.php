<?php

return [
    /*
     * urlencoded, formdata, raw,...
     */
    'default_body_type' => 'urlencoded',

    'filter_uri' => 'api',

    'middleware' => 'api',

    /*
     *
     */
    'login' => [
        'uri' => 'api/auth/login',
        'username' => 'testing@gmail.com',
        'password' => 'password',
    ],

    'excluded_routes' => [
        'documentation',
        'oauth2'
    ],

    'storage_location' => storage_path(),
];
