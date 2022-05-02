<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CoinGecko;

$mainnetExplorer = env('ARKSCAN_MAINNET_EXPLORER_URL', 'https://live.arkscan.io');
$testnetExplorer = env('ARKSCAN_TESTNET_EXPLORER_URL', 'https://test.arkscan.io');

return [
    'network' => env('ARKSCAN_NETWORK', 'development'),

    'uri_prefix' => env('ARKSCAN_URI_PREFIX', 'payvo'),

    'market_data_provider_service' => env('ARKSCAN_MARKET_DATA_PROVIDER_SERVICE', CoinGecko::class),

    'networks' => [
        'production' => [
            'name'               => env('ARKSCAN_NETWORK_NAME', 'ARK Public Network'),
            'alias'              => env('ARKSCAN_NETWORK_ALIAS', 'mainnet'),
            'api'                => env('ARKSCAN_NETWORK_API', 'https://wallets.ark.io/api'),
            'mainnetExplorerUrl' => $mainnetExplorer,
            'testnetExplorerUrl' => $testnetExplorer,
            'currency'           => env('ARKSCAN_NETWORK_CURRENCY', 'ARK'),
            'currencySymbol'     => env('ARKSCAN_NETWORK_CURRENCY_SYMBOL', 'Ѧ'),
            'confirmations'      => intval(env('ARKSCAN_NETWORK_CONFIRMATIONS', 51)),
            'knownWallets'       => env('ARKSCAN_NETWORK_KNOWN_WALLETS', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/mainnet/known-wallets-extended.json'),
            'canBeExchanged'     => env('ARKSCAN_NETWORK_CAN_BE_EXCHANGED', true),
            'hasTimelock'        => env('ARKSCAN_NETWORK_HAS_TIMELOCK', false),
            'epoch'              => env('ARKSCAN_NETWORK_EPOCH', '2017-03-21T13:00:00.000Z'),
            'delegateCount'      => intval(env('ARKSCAN_NETWORK_DELEGATE_COUNT', 51)),
            'blockTime'          => intval(env('ARKSCAN_NETWORK_BLOCK_TIME', 8)),
            'blockReward'        => intval(env('ARKSCAN_NETWORK_BLOCK_REWARD', 2)),
            'base58Prefix'       => intval(env('ARKSCAN_NETWORK_BASE58_PREFIX', 23)),
        ],
        'development' => [
            'name'               => env('ARKSCAN_NETWORK_NAME', 'ARK Development Network'),
            'api'                => env('ARKSCAN_NETWORK_API', 'https://dwallets.ark.io/api'),
            'alias'              => env('ARKSCAN_NETWORK_ALIAS', 'devnet'),
            'mainnetExplorerUrl' => $mainnetExplorer,
            'testnetExplorerUrl' => $testnetExplorer,
            'currency'           => env('ARKSCAN_NETWORK_CURRENCY', 'DARK'),
            'currencySymbol'     => env('ARKSCAN_NETWORK_CURRENCY_SYMBOL', 'DѦ'),
            'confirmations'      => intval(env('ARKSCAN_NETWORK_CONFIRMATIONS', 51)),
            'canBeExchanged'     => env('ARKSCAN_NETWORK_CAN_BE_EXCHANGED', false),
            'hasTimelock'        => env('ARKSCAN_NETWORK_HAS_TIMELOCK', true),
            'epoch'              => env('ARKSCAN_NETWORK_EPOCH', '2017-03-21T13:00:00.000Z'),
            'delegateCount'      => intval(env('ARKSCAN_NETWORK_DELEGATE_COUNT', 51)),
            'blockTime'          => intval(env('ARKSCAN_NETWORK_BLOCK_TIME', 8)),
            'blockReward'        => intval(env('ARKSCAN_NETWORK_BLOCK_REWARD', 2)),
            'base58Prefix'       => intval(env('ARKSCAN_NETWORK_BASE58_PREFIX', 30)),
        ],
    ],

    'statistics' => [

        /*
         * Number of seconds to wait before refreshing the page.
         */
        'refreshInterval' => env('ARKSCAN_STATISTICS_REFRESH_INTERVAL', '60'),
    ],
];
