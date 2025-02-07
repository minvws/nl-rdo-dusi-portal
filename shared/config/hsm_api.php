<?php

declare(strict_types=1);

return [

    'endpoint_url' => env('HSM_API_ENDPOINT_URL'),

    'verify_ssl' => env('HSM_API_VERIFY_SSL', true),

    'client_certificate_path' => env('HSM_API_CLIENT_CERTIFICATE_PATH'),

    'client_certificate_key_path' => env('HSM_API_CLIENT_CERTIFICATE_KEY_PATH'),

    'module' => env('HSM_API_MODULE'),

    'slot' => env('HSM_API_SLOT'),
];
