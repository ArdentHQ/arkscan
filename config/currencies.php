<?php

declare(strict_types=1);

return [
    'decimals' => [
        'crypto'       => env('CURRENCY_DECIMALS_CRYPTO', 18),
        'crypto_small' => env('CURRENCY_DECIMALS_CRYPTO_SMALL', 18),
    ],

    'notation' => [
        'crypto' => env('CURRENCY_NOTATION_CRYPTO', 1e18),
    ],

    'currencies' => [
        'aud' => [
            'currency' => 'AUD',
            'locale'   => 'en_AU',
            'symbol'   => '$',
        ],
        'brl' => [
            'currency' => 'BRL',
            'locale'   => 'pt_BR',
            'symbol'   => 'R$',
        ],
        'btc' => [
            'currency' => 'BTC',
            'locale'   => null,
            'symbol'   => null,
        ],
        'cad' => [
            'currency' => 'CAD',
            'locale'   => 'en_CA',
            'symbol'   => '$',
        ],
        'chf' => [
            'currency' => 'CHF',
            'locale'   => 'fr_CH',
            'symbol'   => null,
        ],
        'cny' => [
            'currency' => 'CNY',
            'locale'   => 'ii_CN',
            'symbol'   => '¥',
        ],
        'eth' => [
            'currency' => 'ETH',
            'locale'   => null,
            'symbol'   => null,
        ],
        'eur' => [
            'currency' => 'EUR',
            'locale'   => 'en_FR',
            'symbol'   => '€',
        ],
        'gbp' => [
            'currency' => 'GBP',
            'locale'   => 'en_GB',
            'symbol'   => '£',
        ],
        'jpy' => [
            'currency' => 'JPY',
            'locale'   => 'ja_JP',
            'symbol'   => '¥',
        ],
        'krw' => [
            'currency' => 'KRW',
            'locale'   => 'ko_KR',
            'symbol'   => '₩',
        ],
        'ltc' => [
            'currency' => 'LTC',
            'locale'   => null,
            'symbol'   => null,
        ],
        'nzd' => [
            'currency' => 'NZD',
            'locale'   => 'en_NZ',
            'symbol'   => '$',
        ],
        'rub' => [
            'currency' => 'RUB',
            'locale'   => 'ru_RU',
            'symbol'   => '₽',
        ],
        'usd' => [
            'currency' => 'USD',
            'locale'   => 'en_US',
            'symbol'   => '$',
        ],
    ],
];
