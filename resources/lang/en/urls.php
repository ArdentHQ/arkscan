<?php

declare(strict_types=1);

return [
    'ipfs'       => 'https://cloudflare-ipfs.com/ipfs/:hash',
    'github'     => 'https://github.com/ardenthq/arkscan',
    'arkvault'   => config('arkscan.urls.public.arkvault', 'https://arkvault.io/'),
    'arkconnect' => config('arkscan.urls.public.arkconnect', 'https://arkconnect.io/'),

    'docs'   => [
        'validator' => 'https://arkvault.io/docs/transactions/validator',
        'arkscan'   => 'https://arkscan.io/docs',
        'api'       => 'https://ark.dev/docs/api',
    ],
];
