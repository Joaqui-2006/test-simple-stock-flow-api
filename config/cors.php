<?php

return [
    'paths' => ['api/*', 'media/*', 'health'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['X-Correlation-ID', 'WWW-Authenticate', 'Allow'],
    'max_age' => 0,
    'supports_credentials' => false,
];