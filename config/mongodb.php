<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default MongoDB Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default MongoDB connection for all MongoDB work.
    |
    */

    'default' => env('MONGODB_CONNECTION', 'mongodb'),

    /*
    |--------------------------------------------------------------------------
    | MongoDB Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the MongoDB connections setup for your application.
    | You can modify the connection options to fit your requirements.
    |
    */

    'connections' => [

        'mongodb' => [
            'driver'     => 'mongodb',
            'host'       => env('MONGODB_HOST', '127.0.0.1'),
            'port'       => env('MONGODB_PORT', 27017),
            'database'   => env('MONGODB_DATABASE', 'realestate_logs'),
            'username'   => env('MONGODB_USERNAME', ''),
            'password'   => env('MONGODB_PASSWORD', ''),
            'options' => [
                'database' => env('MONGODB_AUTH_DATABASE', 'admin'),
            ],
        ],

    ],

];
