<?php

$caBundle = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');

if (! $caBundle && PHP_OS_FAMILY === 'Windows') {
    $xamppCaBundle = dirname(PHP_BINARY, 2).DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'curl-ca-bundle.crt';

    if (is_file($xamppCaBundle)) {
        $caBundle = $xamppCaBundle;
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openstreetmap' => [
        'nominatim_url' => env('OPENSTREETMAP_NOMINATIM_URL', 'https://nominatim.openstreetmap.org'),
        'user_agent' => env('OPENSTREETMAP_USER_AGENT', 'ConnectSpace/1.0'),
        'ca_bundle' => env('OPENSTREETMAP_CA_BUNDLE', $caBundle ?: null),
    ],

];
