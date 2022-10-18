<?php

declare(strict_types=1);

return [
    'driver'    => env('ZENDESK_DRIVER', 'api'),
    'subdomain' => env('ZENDESK_SUBDOMAIN', null),
    'username'  => env('ZENDESK_USERNAME', null),
    'token'     => env('ZENDESK_TOKEN', null),
];
