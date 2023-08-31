<?php

declare(strict_types=1);

return [
    'backend_encrypt' => env('BACKEND_ENCRYPT_UNTIL_FRONTEND_SUPPORT'),
    'public_key' => env('HSM_PUBLIC_KEY_FILE_PATH'),
];
