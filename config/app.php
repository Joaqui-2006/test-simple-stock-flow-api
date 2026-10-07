<?php

return [
    'name' => env('APP_NAME', 'SimpleStockFlow'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost:8000'),
    'timezone' => 'UTC',
    'locale' => 'es',
    'fallback_locale' => 'es',
    'key' => env('APP_KEY', 'base64:7n8p2R1vM4Z5k3X9w8Y7t6U5e4R3q2P1o0I9u8Y7t6U='),
    'cipher' => 'AES-256-CBC',
];