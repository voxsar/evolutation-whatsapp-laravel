<?php

return [
    'base_url' => env('WAAPI_BASE_URL', 'https://your-evolution-api-server.com'),
    'instance' => env('WAAPI_INSTANCE'),
    'apikey' => env('WAAPI_APIKEY'),
    'timeout' => env('WAAPI_TIMEOUT', 180),
    'connect_timeout' => env('WAAPI_CONNECT_TIMEOUT', 180),
    'read_timeout' => env('WAAPI_READ_TIMEOUT', 180),
    'enabled' => env('WAAPI_ENABLED', true),
];