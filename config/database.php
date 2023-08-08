<?php

declare(strict_types=1);

use App\Shared\Models\Connection;

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

    'default' => Connection::APPLICATION,

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
        Connection::APPLICATION => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_APPLICATION_URL'),
            'host' => env('DB_APPLICATION_HOST', '127.0.0.1'),
            'port' => env('DB_APPLICATION_PORT', '5432'),
            'database' => env('DB_APPLICATION_DATABASE', 'forge'),
            'username' => env('DB_APPLICATION_USERNAME', 'forge'),
            'password' => env('DB_APPLICATION_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_APPLICATION_SSLMODE', 'prefer'),
            'sslrootcert' => env('DB_APPLICATION_SSLROOTCERT', null),
            'sslcert' => env('DB_APPLICATION_SSLCERT', null),
            'sslkey' => env('DB_APPLICATION_SSLKEY', null),
        ],
        Connection::FORM => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_FORM_URL'),
            'host' => env('DB_FORM_HOST', '127.0.0.1'),
            'port' => env('DB_FORM_PORT', '5432'),
            'database' => env('DB_FORM_DATABASE', 'forge'),
            'username' => env('DB_FORM_USERNAME', 'forge'),
            'password' => env('DB_FORM_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_FORM_SSLMODE', 'prefer'),
            'sslrootcert' => env('DB_FORM_SSLROOTCERT', null),
            'sslcert' => env('DB_FORM_SSLCERT', null),
            'sslkey' => env('DB_FORM_SSLKEY', null),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */
    'migrations' => 'migrations',

];
