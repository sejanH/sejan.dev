<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WordPress Migration Driver
    |--------------------------------------------------------------------------
    | Options: 'database', 'rest_api', 'xml'
    */
    'driver' => env('WP_MIGRATION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Direct WordPress Database Connection Settings
    |--------------------------------------------------------------------------
    */
    'database' => [
        'connection' => env('WP_DB_CONNECTION', 'mysql'),
        'host'       => env('WP_DB_HOST', '127.0.0.1'),
        'port'       => env('WP_DB_PORT', '3306'),
        'database'   => env('WP_DB_DATABASE', 'wordpress_db'),
        'username'   => env('WP_DB_USERNAME', 'root'),
        'password'   => env('WP_DB_PASSWORD', ''),
        'prefix'     => env('WP_TABLE_PREFIX', 'wp_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WordPress REST API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'url'      => env('WP_API_URL', 'https://blog.example.com/wp-json/wp/v2'),
        'username' => env('WP_API_USER', 'admin'),
        'password' => env('WP_API_APP_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Handling Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'uploads_path'        => env('WP_UPLOADS_PATH', '/var/www/sejan.xyz/wp-content/uploads'),
        'download'            => env('WP_DOWNLOAD_MEDIA', true),
        'disk'                => env('WP_MEDIA_DISK', 'public'),
        'path'                => env('WP_MEDIA_DIRECTORY', 'media'),
        'replace_inline_urls' => env('WP_MEDIA_REPLACE_INLINE_URLS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Registration Settings
    |--------------------------------------------------------------------------
    | Controls whether registration is allowed (Strictly false for admin-only setup)
    */
    'allow_registration' => false,
];
