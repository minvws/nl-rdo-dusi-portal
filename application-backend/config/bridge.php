<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\Backend\Services\MessageService;
use MinVWS\DUSi\Shared\Bridge\Ping\Services\PingService;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

$bindings = [
    'ping' => [
        'paramsClass' => Ping::class,
        'callback' => [PingService::class, 'ping']
    ],
    RPCMethods::LIST_MESSAGES => [
        'paramsClass' => MessageListParams::class,
        'callback' => [MessageService::class, 'listMessages']
    ]
];

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

    'servers' => [
        'default' => [
            'connection' => 'default',
            'bindings' => $bindings
        ]
    ],

    'defaultServer' => 'default'
];
