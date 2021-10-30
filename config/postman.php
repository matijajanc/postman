<?php

return [
    /*
     * Specify default body type.
     * This can be overridden with "mode" property in Postman attribute
     * #[Postman(['mode' => 'raw'])]
     *
     * urlencoded, formdata, raw,...
     */
    'default_body_type' => 'urlencoded',

    /*
     * If you want only specific route in your Postman file, you can filter them by "uri"
     * In this case, only routes which starts with "api/..." with be left in
     */
    'filter_uri' => 'api',

    /**
     * Routes which uses this middleware will be set with Authorization bearer token
     */
    'middleware' => 'api',

    /*
     * Login API that'll return bearer token in bellow format and will update {{token}} variable in Postman
     * {
            "success": true,
            "records": {
                "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9...",
                "token_type": "bearer",
                "expires_in": 3600
            }
        }
     */
    'login' => [
        'uri' => 'api/auth/login',
        'username' => 'testing@domain.com',
        'password' => 'password',
    ],

    /*
     * Excluded routes which str_contains()
     */
    'excluded_routes' => [
        'documentation',
        'oauth2'
    ],

    /*
     * Default disk location of generated Postman files
     * local, public, s3,...
     */
    'storage_disk' => 'local',
];
