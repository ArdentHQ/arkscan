<?php

declare(strict_types=1);

use App\Services\MarketDataProviders\CoinGecko;

$mainnetExplorer = env('ARKSCAN_MAINNET_EXPLORER_URL', 'https://live.arkscan.io');
$testnetExplorer = env('ARKSCAN_TESTNET_EXPLORER_URL', 'https://test.arkscan.io');

return [
    'network'                           => env('ARKSCAN_NETWORK', 'development'),

    'vault_url'                         => env('ARKSCAN_VAULT_URL', 'https://app.arkvault.io/#/'),

    'market_data_provider_service'      => env('ARKSCAN_MARKET_DATA_PROVIDER_SERVICE', CoinGecko::class),

    'networks'                          => [
        'production'  => [
            'coin'                => env('ARKSCAN_NETWORK_COIN', 'Mainsail'),
            'name'                => env('ARKSCAN_NETWORK_NAME', 'ARK Public Network'),
            'alias'               => env('ARKSCAN_NETWORK_ALIAS', 'mainnet'),
            'api'                 => env('ARKSCAN_NETWORK_API', 'https://wallets.mainsailhq.com/api'),
            'nethash'             => env('ARKSCAN_NETHASH', '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988'),
            'mainnetExplorerUrl'  => $mainnetExplorer,
            'testnetExplorerUrl'  => $testnetExplorer,
            'currency'            => env('ARKSCAN_NETWORK_CURRENCY', 'ARK'),
            'currencySymbol'      => env('ARKSCAN_NETWORK_CURRENCY_SYMBOL', 'Ѧ'),
            'confirmations'       => intval(env('ARKSCAN_NETWORK_CONFIRMATIONS', 51)),
            'knownWallets'        => env('ARKSCAN_NETWORK_KNOWN_WALLETS', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/mainnet/known-wallets-extended.json'),
            'canBeExchanged'      => env('ARKSCAN_NETWORK_CAN_BE_EXCHANGED', true),
            'epoch'               => env('ARKSCAN_NETWORK_EPOCH', '2017-03-21T13:00:00.000Z'),
            'validatorCount'      => intval(env('ARKSCAN_NETWORK_VALIDATOR_COUNT', 53)),
            'blockTime'           => intval(env('ARKSCAN_NETWORK_BLOCK_TIME', 8)),
            'blockReward'         => intval(env('ARKSCAN_NETWORK_BLOCK_REWARD', 2)),
            'base58Prefix'        => intval(env('ARKSCAN_NETWORK_BASE58_PREFIX', 23)),

            'contract_addresses' => [],

            'contract_methods' => [],
        ],
        'development' => [
            'coin'                => env('ARKSCAN_NETWORK_COIN', 'Mainsail'),
            'name'                => env('ARKSCAN_NETWORK_NAME', 'ARK Development Network'),
            'api'                 => env('ARKSCAN_NETWORK_API', 'https://dwallets.mainsailhq.com/api'),
            'alias'               => env('ARKSCAN_NETWORK_ALIAS', 'devnet'),
            'nethash'             => env('ARKSCAN_NETHASH', '7b9a7c6a14d3f8fb3f47c434b8c6ef0843d5622f6c209ffeec5411aabbf4bf1c'),
            'mainnetExplorerUrl'  => $mainnetExplorer,
            'testnetExplorerUrl'  => $testnetExplorer,
            'currency'            => env('ARKSCAN_NETWORK_CURRENCY', 'DARK'),
            'currencySymbol'      => env('ARKSCAN_NETWORK_CURRENCY_SYMBOL', 'DѦ'),
            'confirmations'       => intval(env('ARKSCAN_NETWORK_CONFIRMATIONS', 51)),
            'knownWallets'        => env('ARKSCAN_NETWORK_KNOWN_WALLETS', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/mainsail/devnet/known-wallets-extended.json'),
            'canBeExchanged'      => env('ARKSCAN_NETWORK_CAN_BE_EXCHANGED', false),
            'epoch'               => env('ARKSCAN_NETWORK_EPOCH', '2023-12-21T00:00:00.000Z'),
            'validatorCount'      => intval(env('ARKSCAN_NETWORK_VALIDATOR_COUNT', 53)),
            'blockTime'           => intval(env('ARKSCAN_NETWORK_BLOCK_TIME', 8)),
            'blockReward'         => intval(env('ARKSCAN_NETWORK_BLOCK_REWARD', 2)),
            'base58Prefix'        => intval(env('ARKSCAN_NETWORK_BASE58_PREFIX', 30)),

            'contract_addresses' => [
                'consensus'    => env('ARKSCAN_CONTRACT_CONSENSUS_ADDRESS', '0x535B3D7A252fa034Ed71F0C53ec0C6F784cB64E1'),
                'multipayment' => env('ARKSCAN_CONTRACT_MULTIPAYMENT_ADDRESS', '0x83769BeEB7e5405ef0B7dc3C66C43E3a51A6d27f'),
                'username'     => env('ARKSCAN_CONTRACT_USERNAME_ADDRESS', '0x2c1DE3b4Dbb4aDebEbB5dcECAe825bE2a9fc6eb6'),
            ],

            'contract_methods' => [
                'transfer'               => env('ARKSCAN_CONTRACT_TRANSFER_METHOD', 'a9059cbb'),
                'multipayment'           => env('ARKSCAN_CONTRACT_MULTIPAYMENT_METHOD', '084ce708'),
                'validator_registration' => env('ARKSCAN_CONTRACT_VALIDATOR_REGISTRATION_METHOD', '602a9eee'),
                'vote'                   => env('ARKSCAN_CONTRACT_VOTE_METHOD', '6dd7d8ea'),
                'unvote'                 => env('ARKSCAN_CONTRACT_UNVOTE_METHOD', '3174b689'),
                'validator_resignation'  => env('ARKSCAN_CONTRACT_VALIDATOR_RESIGNATION_METHOD', 'b85f5da2'),
                'username_registration'  => env('ARKSCAN_CONTRACT_USERNAME_REGISTRATION_METHOD', '36a94134'),
                'username_resignation'   => env('ARKSCAN_CONTRACT_USERNAME_RESIGNATION_METHOD', 'ebed6dab'),
                'contract_deployment'    => env('ARKSCAN_CONTRACT_DEPLOYMENT_METHOD', '60806040'),
            ],
        ],
    ],

    'productivity' => [
        'danger'  => env('ARKSCAN_PRODUCTIVITY_DANGER', 97),
        'warning' => env('ARKSCAN_PRODUCTIVITY_WARNING', 99.8),
    ],

    'pagination' => [
        'per_page' => env('ARKSCAN_PAGINATION_PER_PAGE', 25),
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

    'throttle'                           => [
        'wallet_submitted' => [
            'max_attempts' => env('THROTTLE_WALLET_SUBMITTED_MAX_ATTEMPTS', 3),
            'duration'     => env('THROTTLE_WALLET_SUBMITTED_DURATION', 3600),
        ],
    ],

    'exchanges' => [
        'list_src' => env('EXCHANGES_LIST_SRC', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/arkscan/exchanges.json'),
        'icon_url' => env('EXCHANGES_ICON_URL', 'https://raw.githubusercontent.com/ArkEcosystem/common/master/arkscan/icons/'),
    ],

    'scout' => [
        'run_jobs' => env('SCOUT_RUN_JOBS', false),
    ],

    'arkconnect' => [
        'enabled' => env('ARKCONNECT_ENABLED', false),
    ],

    'market_data' => [
        'coingecko' => [
            'exception_frequency' => env('COINGECKO_EXCEPTION_FREQUENCY', 60),
            'ignore_errors'       => env('COINGECKO_EXCEPTION_IGNORE_ERRORS', false),
        ],

        'cryptocompare' => [
            'exception_frequency' => env('CRYPTOCOMPARE_EXCEPTION_FREQUENCY', 60),
            'ignore_errors'       => env('CRYPTOCOMPARE_EXCEPTION_IGNORE_ERRORS', false),
        ],
    ],

    'webhooks' => [
        'block-applied' => [
            'ttl' => (int) env('ARKSCAN_WEBHOOKS_BLOCK_APPLIED_TTL', 8),
        ],
        'transaction-applied' => [
            'ttl' => (int) env('ARKSCAN_WEBHOOKS_TRANSACTION_APPLIED_TTL', 8),
        ],
        'wallet-vote' => [
            'ttl' => (int) env('ARKSCAN_WEBHOOKS_WALLET_VOTE_TTL', 8),
        ],
        'currency-update' => [
            'ttl' => (int) env('ARKSCAN_WEBHOOKS_CURRENCY_UPDATE_TTL', 8),
        ],
        'statistics-update' => [
            'ttl' => (int) env('ARKSCAN_WEBHOOKS_STATISTICS_UPDATE_TTL', 8),
        ],
    ],
];
