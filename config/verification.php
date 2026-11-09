<?php

return [
    // none          = no registry connected, humans review every company (works out of the box)
    // opencorporates = also look the registration number up on OpenCorporates (needs a token)
    'registry_driver' => env('VERIFY_REGISTRY_DRIVER', 'none'),

    'opencorporates' => [
        'token'        => env('OPENCORPORATES_TOKEN'),
        'jurisdiction' => env('OPENCORPORATES_JURISDICTION', 'bd'),
    ],

    // Logins (normal users table) allowed to open /admin/verifications, comma separated in .env
    'admin_emails' => array_values(array_filter(array_map('trim', explode(',', env('VERIFY_ADMIN_EMAILS', ''))))),

    'free_email_domains' => [
        'gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com', 'live.com',
        'icloud.com', 'proton.me', 'protonmail.com', 'aol.com',
    ],
];
