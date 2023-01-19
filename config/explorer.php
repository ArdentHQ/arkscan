<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CoinGecko;

$mainnetExplorer = env('ARKSCAN_MAINNET_EXPLORER_URL', 'https://live.arkscan.io');
$testnetExplorer = env('ARKSCAN_TESTNET_EXPLORER_URL', 'https://test.arkscan.io');

return [
    'network'                           => env('ARKSCAN_NETWORK', 'development'),

    'vault_url'                         => env('ARKSCAN_VAULT_URL', 'https://app.arkvault.io/#/'),

    'market_data_provider_service'      => env('ARKSCAN_MARKET_DATA_PROVIDER_SERVICE', CoinGecko::class),

    'coingecko_exception_frequency'     => env('COINGECKO_EXCEPTION_FREQUENCY', 60),

    'cryptocompare_exception_frequency' => env('CRYPTOCOMPARE_EXCEPTION_FREQUENCY', 60),

    'migration_address'                 => env('ARKSCAN_MIGRATION_ADDRESS'),
    'migration' => [
        'minimum_fee'    => env('ARKSCAN_MIGRATION_MIN_FEE', 5000000),
        'minimum_amount' => env('ARKSCAN_MIGRATION_MIN_AMOUNT', 100000000),
    ],

    'networks'                          => [
        'production'  => [
            'name'               => env('ARKSCAN_NETWORK_NAME', 'ARK Public Network'),
            'alias'              => env('ARKSCAN_NETWORK_ALIAS', 'mainnet'),
            'api'                => env('ARKSCAN_NETWORK_API', 'https://wallets.ark.io/api'),
            'nethash'            => env('ARKSCAN_NETHASH', '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988'),
            'mainnetExplorerUrl' => $mainnetExplorer,
            'testnetExplorerUrl' => $testnetExplorer,
            'polygonExplorerUrl' => env('POLYGON_EXPLORER_URL', 'https://polygonscan.com'),
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
            'nethash'            => env('ARKSCAN_NETHASH', '2a44f340d76ffc3df204c5f38cd355b7496c9065a1ade2ef92071436bd72e867'),
            'mainnetExplorerUrl' => $mainnetExplorer,
            'testnetExplorerUrl' => $testnetExplorer,
            'polygonExplorerUrl' => env('POLYGON_EXPLORER_URL', 'https://mumbai.polygonscan.com'),
            'currency'           => env('ARKSCAN_NETWORK_CURRENCY', 'DARK'),
            'currencySymbol'     => env('ARKSCAN_NETWORK_CURRENCY_SYMBOL', 'DѦ'),
            'confirmations'      => intval(env('ARKSCAN_NETWORK_CONFIRMATIONS', 51)),
            'knownWallets'       => env('ARKSCAN_NETWORK_KNOWN_WALLETS', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/devnet/known-wallets-extended.json'),
            'canBeExchanged'     => env('ARKSCAN_NETWORK_CAN_BE_EXCHANGED', false),
            'hasTimelock'        => env('ARKSCAN_NETWORK_HAS_TIMELOCK', true),
            'epoch'              => env('ARKSCAN_NETWORK_EPOCH', '2017-03-21T13:00:00.000Z'),
            'delegateCount'      => intval(env('ARKSCAN_NETWORK_DELEGATE_COUNT', 51)),
            'blockTime'          => intval(env('ARKSCAN_NETWORK_BLOCK_TIME', 8)),
            'blockReward'        => intval(env('ARKSCAN_NETWORK_BLOCK_REWARD', 2)),
            'base58Prefix'       => intval(env('ARKSCAN_NETWORK_BASE58_PREFIX', 30)),
        ],
    ],

    'statistics'                        => [

        /*
         * Number of seconds to wait before refreshing the page.
         */
        'refreshInterval' => env('ARKSCAN_STATISTICS_REFRESH_INTERVAL', '60'),
    ],

    'support'                           => [
        'enabled' => env('ARKSCAN_SUPPORT_ENABLED', false),
    ],
];
