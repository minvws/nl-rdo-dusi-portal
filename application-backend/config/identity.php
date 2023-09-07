<?php

declare(strict_types=1);

return [
    'hash_algorithm' => env('IDENTITY_HASH_ALGORITHM', 'sha256'),
    'hash_secret' => env('IDENTITY_HASH_SECRET')
];
