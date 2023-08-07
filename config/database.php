<?php

declare(strict_types=1);

use App\Models\Connection;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

        'default' => Connection::FORM,

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        Connection::USER => [
            'driver' => 'pgsql',
            'url' => env('USER_DATABASE_URL'),
            'host' => env('USER_DB_HOST', '127.0.0.1'),
            'port' => env('USER_DB_PORT', '5432'),
            'database' => env('USER_DB_DATABASE', 'forge'),
            'username' => env('USER_DB_USERNAME', 'forge'),
            'password' => env('USER_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
        Connection::FORM => [
            'driver' => 'pgsql',
            'url' => env('FORM_DATABASE_URL'),
            'host' => env('FORM_DB_HOST', '127.0.0.1'),
            'port' => env('FORM_DB_PORT', '5432'),
            'database' => env('FORM_DB_DATABASE', 'forge'),
            'username' => env('FORM_DB_USERNAME', 'forge'),
            'password' => env('FORM_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
        Connection::APPLICATION => [
            'driver' => 'pgsql',
            'url' => env('APPLICATION_DATABASE_URL'),
            'host' => env('APPLICATION_DB_HOST', '127.0.0.1'),
            'port' => env('APPLICATION_DB_PORT', '5432'),
            'database' => env('APPLICATION_DB_DATABASE', 'forge'),
            'username' => env('APPLICATION_DB_USERNAME', 'forge'),
            'password' => env('APPLICATION_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

    ],
    //
    //    /*
    //    |--------------------------------------------------------------------------
    //    | Migration Repository Table
    //    |--------------------------------------------------------------------------
    //    |
    //    | This table keeps track of all the migrations that have already run for
    //    | your application. Using this information, we can determine which of
    //    | the migrations on disk haven't actually been run in the database.
    //    |
    //    */
    //
        'migrations' => 'migrations',

];
