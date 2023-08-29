<?php

declare(strict_types=1);

use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Bridge\Ping\Services\PingService;

$bindings = [
    'ping' => [
        'paramsClass' => Ping::class,
        'callback' => array(PingService::class, 'ping')
    ]
];

return [
    'connections' => [
        'default' => [
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => (int)env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASSWORD', 'guest'),
            'queue' => env('BRIDGE_RPC_QUEUE', 'rpc_queue'),
        ]
    ],

    'defaultConnection' => 'default',

    'servers' => [
        'default' => [
            'connection' => 'default',
            'bindings' => $bindings
        ]
    ],

    'defaultServer' => 'default'
];
