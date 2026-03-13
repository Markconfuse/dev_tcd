<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'google' => [
        // 'client_id' => '733207888015-i2hslheinvr0kbilt4paekegkc3e59tp.apps.googleusercontent.com',
        'client_id' => '700627403653-dq21lrqu9h9g5apk69igdpu2pto14min.apps.googleusercontent.com',
        // 'client_secret' => 'YfnlHO03ci81ZuZbkYyG5Hnx',
        'client_secret' => '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',

        'redirect' => 'http://localhost:7000/google/callback',
        // 'redirect' => 'http://fa16605.ics.com.ph/Procurement/google/callback',
    ],

];
