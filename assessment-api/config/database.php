<?php

declare(strict_types=1);

use Illuminate\Database\DBAL\TimestampType;
use Illuminate\Support\Str;
use MinVWS\DUSi\Shared\Application\Models\Connection as ApplicationConnection;
use MinVWS\DUSi\Shared\User\Models\Connection as UserConnection;

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

    'default' => ApplicationConnection::APPLICATION,

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
        UserConnection::USER => [
            'driver' => 'pgsql',
            'url' => env('USER_DATABASE_URL'),
            'host' => env('DB_USER_HOST', '127.0.0.1'),
            'port' => env('DB_USER_PORT', '5432'),
            'database' => env('DB_USER_DATABASE', 'forge'),
            'username' => env('DB_USER_USERNAME', 'forge'),
            'password' => env('DB_USER_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_USER_SSLMODE', 'prefer'),
            'sslrootcert' => env('DB_USER_SSLROOTCERT', null),
            'sslcert' => env('DB_USER_SSLCERT', null),
            'sslkey' => env('DB_USER_SSLKEY', null),
        ],
        ApplicationConnection::APPLICATION => [
            'driver' => 'pgsql',
            'url' => env('APPLICATION_DATABASE_URL'),
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

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
            'context' => [
                // TLS Config for phpredis
                'stream' => [
                    'peer_name' => env('REDIS_TLS_PEER_NAME', ''),
                    'verify_peer' => env('REDIS_TLS_VERIFY_PEER', true),
                    'verify_peer_name' => env('REDIS_TLS_VERIFY_PEER_NAME', true),
                    'cafile' => env('REDIS_TLS_CAFILE', ''),
                    'local_cert' => env('REDIS_TLS_LOCAL_CERT', ''),
                    'local_pk' => env('REDIS_TLS_LOCAL_PK', ''),
                ]
            ]
        ],

        'default' => [
            'scheme' => env('REDIS_SCHEME', 'tcp'),
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            // TLS Config for predis
            'ssl' => [
                'peer_name' => env('REDIS_TLS_PEER_NAME', ''),
                'verify_peer' => env('REDIS_TLS_VERIFY_PEER', true),
                'verify_peer_name' => env('REDIS_TLS_VERIFY_PEER_NAME', true),
                'cafile' => env('REDIS_TLS_CAFILE', ''),
                'local_cert' => env('REDIS_TLS_LOCAL_CERT', ''),
                'local_pk' => env('REDIS_TLS_LOCAL_PK', ''),
            ]
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '1'),
            // TLS Config for predis
            'ssl' => [
                'peer_name' => env('REDIS_TLS_PEER_NAME', ''),
                'verify_peer' => env('REDIS_TLS_VERIFY_PEER', true),
                'verify_peer_name' => env('REDIS_TLS_VERIFY_PEER_NAME', true),
                'cafile' => env('REDIS_TLS_CAFILE', ''),
                'local_cert' => env('REDIS_TLS_LOCAL_CERT', ''),
                'local_pk' => env('REDIS_TLS_LOCAL_PK', ''),
            ]
        ],

    ],
    'dbal' => [
        'types' => [
            'timestamp' => TimestampType::class,
        ],
    ],
];
