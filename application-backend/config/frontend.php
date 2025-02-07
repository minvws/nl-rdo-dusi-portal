<?php

declare(strict_types=1);

return [

    /**
     * Frontend uses sodium crypto_box_seal() to encrypt data.
     * The public key is used to encrypt the data.
     * The private key is used to decrypt the data.
     *
     * Key pair can be generated with: php artisan sodium:generate-key-pair
     */
    'form_encryption' => [
        /**
         * Base64 encoded sodium public key of key pair.
         */
        'public_key' => env('FE_FORM_ENCRYPTION_PUBLIC_KEY'),

        /**
         * Base64 encoded sodium private key of key pair.
         */
        'private_key' => env('FE_FORM_ENCRYPTION_PRIVATE_KEY'),
    ]

];
