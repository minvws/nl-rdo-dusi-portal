<?php

declare(strict_types=1);

return [
    'connections' => [
        'default' => [
            'host' => env('RABBITMQ_HOST', 'rabbitmq'),
            'port' => (int)env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'queue' => env('BRIDGE_RPC_QUEUE', 'rpc_queue'),
        ]
    ],

    'defaultConnection' => 'default',

    'servers' => [],

    'defaultServer' => 'default'
];
