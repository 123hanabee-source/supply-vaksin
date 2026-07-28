<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // vaccine.html is now served from this same Laravel app's public/ folder
    // (e.g. http://localhost/vaccine.html), so this is same-origin and these
    // entries matter less — kept here in case you ever split the frontend out again.
    'allowed_origins' => [
        'http://localhost',
        'http://supplyvaksin.test',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Required so the session cookie (used for login state) is sent/received.
    'supports_credentials' => true,
];
