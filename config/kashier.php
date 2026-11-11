<?php

return [
    'mid' => env('KASHIER_MID', 'MID-48236-645'),
    'api_key' => env('KASHIER_API_KEY', '93330df5-2d64-4146-8439-d1a97ad0b580'),
    'secret_key' => env('KASHIER_SECRET_KEY', '87c0f30a0bc90e6806b3fceb1a78e3ab$037f909f6f512ef3e3be35b84b51453195ee2b4672bfadd868ba319e75cea6a07b609c7c9b0220365b0fc85898c327cc'),
    'mode' => env('KASHIER_MODE', 'test'),
    'currency' => env('KASHIER_CURRENCY', 'EGP'),

    'commission_percent' => (float) env('KASHIER_COMMISSION_PERCENT', 2.75),
    'commission_fixed' => (float) env('KASHIER_COMMISSION_FIXED', 3.00),

    'endpoints' => [
        'test' => 'https://test-api.kashier.io',
        'live' => 'https://api.kashier.io',
    ],
];
