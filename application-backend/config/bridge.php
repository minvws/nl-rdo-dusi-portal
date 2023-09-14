<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\Backend\Services\ActionableService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationFileService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMutationService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationRetrievalService;
use MinVWS\DUSi\Application\Backend\Services\ApplicationMessageService;
use MinVWS\DUSi\Shared\Bridge\Ping\Services\PingService;
use MinVWS\DUSi\Shared\Bridge\Ping\DTO\Ping;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ActionableCountsParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFindOrCreateParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationParams;
use MinVWS\DUSi\Shared\Serialisation\Models\Application\ApplicationFileParams;
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
    RPCMethods::FIND_OR_CREATE_APPLICATION => [
        'paramsClass' => ApplicationFindOrCreateParams::class,
        'callback' => [ApplicationMutationService::class, 'findOrCreateApplication']
    ],
    RPCMethods::UPLOAD_APPLICATION_FILE => [
        'paramsClass' => ApplicationFindOrCreateParams::class,
        'callback' => [ApplicationFileService::class, 'saveApplicationFile']
    ],
    RPCMethods::SAVE_APPLICATION => [
        'paramsClass' => ApplicationFindOrCreateParams::class,
        'callback' => [ApplicationMutationService::class, 'saveApplication']
    ],
    RPCMethods::LIST_APPLICATIONS => [
        'paramsClass' => ApplicationListParams::class,
        'callback' => [ApplicationRetrievalService::class, 'listApplications']
    ],
    RPCMethods::GET_APPLICATION => [
        'paramsClass' => ApplicationParams::class,
        'callback' => [ApplicationRetrievalService::class, 'getApplication']
    ],
    RPCMethods::GET_APPLICATION_FILE => [
        'paramsClass' => ApplicationFileParams::class,
        'callback' => [ApplicationFileService::class, 'getApplicationFile']
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

    'declare_exchange_and_queue' => env('BRIDGE_DECLARE_EXCHANGE_AND_QUEUE', true),

    'servers' => [
        'default' => [
            'connection' => 'default',
            'bindings' => $bindings
        ]
    ],

    'defaultServer' => 'default'
];
