<?php

declare(strict_types=1);

return [
    'enabled' => env('SUREPAY_ENABLED', true),
    'key' => env('SUREPAY_KEY'),
    'secret' => env('SUREPAY_SECRET'),
    'endpoint' => env('SUREPAY_ENDPOINT'),
    'verify_ssl' => env('SUREPAY_VERIFY_SSL', true),
    'proxy' => [
        'http' => env('SUREPAY_HTTP_PROXY', ''),
        'https' => env('SUREPAY_HTTPS_PROXY', ''),
    ],
    'debug' => env('SUREPAY_DEBUG', false),
    'request_timeout_seconds' => env('SUREPAY_REQUEST_TIMEOUT_SECONDS', 3),
    'connect_timeout_seconds' => env('SUREPAY_CONNECT_TIMEOUT_SECONDS', 3),
    'endpoint_request_accesstoken' =>
        env('SUREPAY_ENDPOINT_REQUEST_ACCESSTOKEN', 'oauth/client_credential/accesstoken'),
    'endpoint_check_organisations' =>
        env('SUREPAY_ENDPOINT_CHECK_ORGANISATIONS', 'account/check/organisations'),

    /**
     * Here you can configure the specific log channel for SurePay.
     * The default LOG_CHANNEL is used as the log channel.
     *
     * @link https://laravel.com/docs/10.x/logging#configuration
     */
    'log_channel' => env('SUREPAY_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),

    /**
     * Here you can configure the maximum number of retries for the SurePay API.
     * We will retry a request when the API returns a 5xx error or specific 400 errors.
     */
    'max_retries' => env('SUREPAY_MAX_RETRIES', 3),
];
