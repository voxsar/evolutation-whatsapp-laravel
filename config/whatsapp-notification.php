<?php

return [
    'base_url' => env('WAAPI_BASE_URL', 'https://waapi.app/api/v1/instances/'),
    'instance_id' => env('WAAPI_INSTANCE_ID'),
    'token' => env('WAAPI_TOKEN'),
    'timeout' => env('WAAPI_TIMEOUT', 180),
    'connect_timeout' => env('WAAPI_CONNECT_TIMEOUT', 180),
    'read_timeout' => env('WAAPI_READ_TIMEOUT', 180),
    'enabled' => env('WAAPI_ENABLED', true),
];
