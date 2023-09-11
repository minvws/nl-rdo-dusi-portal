<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\Backend\Services\ActionableService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationRetrievalService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMessageService;
use MinVWS\DUSi\Shared\Bridge\Ping\Services\PingService;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageDownloadParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageListParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\MessageParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\RPCMethods;

$bindings = [
    'ping' => [
        'paramsClass' => Ping::class,
        'callback' => [PingService::class, 'ping']
    ],
    RPCMethods::LIST_APPLICATIONS => [
        'paramsClass' => ApplicationListParams::class,
        'callback' => [ApplicationRetrievalService::class, 'listApplications']
    ],
    RPCMethods::GET_APPLICATION => [
        'paramsClass' => ApplicationListParams::class,
        'callback' => [ApplicationRetrievalService::class, 'getApplication']
    ],
    RPCMethods::LIST_MESSAGES => [
        'paramsClass' => MessageListParams::class,
        'callback' => [ApplicationMessageService::class, 'listMessages']
    ],

    RPCMethods::GET_MESSAGE => [
        'paramsClass' => MessageParams::class,
        'callback' => [ApplicationMessageService::class, 'getMessage']
    ],
    RPCMethods::GET_MESSAGE_DOWNLOAD => [
        'paramsClass' => MessageDownloadParams::class,
        'callback' => [ApplicationMessageService::class, 'getMessageDownload']
    ],
    RPCMethods::GET_ACTIONABLE_COUNTS => [
        'paramsClass' => ActionableCountsParams::class,
        'callback' => [ActionableService::class, 'getActionableCounts']
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
