<?php

declare(strict_types=1);

return [

    'endpoint_url' => env('HSM_API_ENDPOINT_URL'),

    'client_certificate_path' => env('HSM_API_CLIENT_CERTIFICATE_PATH'),

    'client_certificate_key_path' => env('HSM_API_CLIENT_CERTIFICATE_KEY_PATH'),

    'module' => env('HSM_API_MODULE'),

    'slot' => env('HSM_API_SLOT'),

    'encryption_key_label' => env('HSM_API_ENCRYPTION_KEY_LABEL'),
];
