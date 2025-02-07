<?php

declare(strict_types=1);

return [
    'public_key_path' => env('HSM_PUBLIC_KEY_FILE_PATH'),

    'key_label' => env('HSM_ENCRYPTION_KEY_LABEL'),
];
