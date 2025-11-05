<?php

return [
    'central_domain' => env('CENTRAL_DOMAIN', parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),

    'features' => [
        'impersonation' => true,
    ],

    'impersonation' => [
        'guard' => 'web',
        'redirect_to' => '/app',
    ],

    'panels' => [
        'app' => [
            'path' => 'app',
        ],
    ],
];
