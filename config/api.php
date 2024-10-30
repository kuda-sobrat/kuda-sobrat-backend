<?php
return [
    'paths' => [
        'api' => ['api/*'],
        'public' => ['public/*'],
    ],
    'url' => [
        'api' => env('API_URL', 'http://localhost'),
    ],
    'prefix' => 'api',
];
