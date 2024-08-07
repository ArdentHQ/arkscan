<?php

declare(strict_types=1);

return [
    'ardent'              => 'Ardent',
    'address'             => 'Address',
    'beta_uppercase'      => 'BETA',
    'optional'            => 'Optional',
    'or'                  => 'or',
    'arkscan'             => 'ARK Scan',
    'scan'                => 'Scan',
    'height'              => 'Height',
    'network'             => 'Network',
    'current_supply'      => 'Current Supply',
    'not_available'       => 'Not Available',
    'price'               => 'Price',
    'market_cap'          => 'Market Cap',
    'verified_address'    => 'Verified Address',
    'exchange'            => 'Exchange',
    'reload'              => 'Reload',
    'confirmed'           => 'Confirmed',
    'see_all'             => 'See all',
    'wallet_not_found'    => '<span class="bg-theme-warning-100">:0</span> has no balance. <br/> <span class="text-base font-normal">Return to this page after the address has received a transaction.</span>',
    'fiat_excluding_self' => 'Excluding :amount sent to self',
    'more_details'        => 'For more :transactionType details',
    'learn_more'          => 'Learn more',
    'confirmations'       => ':count Confirmation|:count Confirmations',
    'confirmations_only'  => 'Confirmations',
    'market_data_by'      => 'Market Data by',
    'select_theme'        => 'Select Theme',
    'select_network'      => 'Select Network',
    'na'                  => 'N/A',
    'all'                 => 'All',
    'overview'            => 'Overview',
    'view'                => 'View',
    'coming_soon'         => 'Coming Soon',
    'select_all'          => 'Select All',
    'success'             => 'Success',
    'information'         => 'Information',
    'error'               => 'Error',
    'warning'             => 'Warning',
    'custom'              => 'Custom',
    'x_of_y'              => ':0 of :1',
    'filter'              => 'Filter',
    'now'                 => 'Now',
    'vote_with'           => 'Vote With',

    'arkconnect' => [
        'mainnet_network'               => 'You\'re viewing data from the main network, but your wallet is connected to test network (ARK Testnet). To use ARK Scan, please switch to <a class="link font-semibold" href="https://test.arkscan.io/">test.arkscan.io</a>.',
        'testnet_network'               => 'You\'re viewing data from the test network, but your wallet is connected to main network (ARK Mainnet). To use ARK Scan, please switch to <a class="link font-semibold" href="https://live.arkscan.io/">live.arkscan.io</a>.',
        'delegate_resigned'             => '<span class="font-semibold" x-text="votedDelegateName"></span>, the delegate you are voting for, has resigned.',
        'delegate_resigned_switch_vote' => 'We recommend switching your vote to an active delegate.',
        'delegate_standby'              => 'The delegate you are voting for <span class="font-semibold">(<span x-text="votedDelegateName"></span>)</span> is not in an active forging position at the moment.',
        'view_delegates'                => 'View Delegates.',

        'connect_wallet_tooltip' => 'Connect Wallet to enable this action.',

        'wrong_network' => [
            'mainnet' => 'You\'re connected with a testnet address. Switch to test.arkscan.io to enable this action.',
            'devnet'  => 'You\'re connected with a mainnet address. Switch to live.arkscan.io to enable this action.',
        ],
    ],

    'navbar' => [
        'search_placeholder' => 'Address / Tx ID / Block ID',
        'mainnet'            => 'Mainnet',
        'testnet'            => 'Testnet',
        'price'              => 'Price',
        'connect_wallet'     => 'Connect Wallet',

        'theme' => [
            'light' => 'Light',
            'dark'  => 'Dark',
            'dim'   => 'Dim',
        ],

        'arkconnect' => [
            'my_address'   => 'My Address',
            'copy_address' => 'Copy Address',
            'disconnect'   => 'Disconnect',

            'modal' => [
                'install_title'               => 'Install ARK Connect',
                'install_subtitle'            => 'To link up on ARK Scan using ARK Connect, you must initially install the extension.',
                'install_arkconnect'          => 'Install ARK Connect',
                'unsupported_browser_title'   => 'Unsupported Browser for ARK Connect',
                'unsupported_browser_warning' => 'ARK Connect is not compatible with your current browser. To install the ARK Connect extension, please switch to a browser based on Chrome or Firefox.',
            ],
        ],
    ],

    'block'                   => [
        'id'            => 'ID',
        'timestamp'     => 'Timestamp',
        'generated_by'  => 'Generated By',
        'height'        => 'Height',
        'transactions'  => 'Transactions',
        'tx'            => 'Tx',
        'amount'        => 'Amount',
        'fee'           => 'Fee',
        'reward'        => 'Reward',
        'confirmations' => 'Confirmations',
    ],

    'transaction'             => [
        'id'                      => 'ID',
        'timestamp'               => 'Timestamp',
        'type'                    => 'Type',
        'sender'                  => 'Sender',
        'recipient'               => 'Recipient',
        'amount'                  => 'Amount',
        'fee'                     => 'Fee',
        'confirmations'           => 'Confirmations',
        'block_id'                => 'Block ID',
        'well-confirmed'          => 'Well Confirmed',
        'voting_delegate'         => '<span class="font-semibold text-theme-secondary-500">Voting for <span class="text-white">:delegate</span></span>',
        'vote_delegate'           => '<span class="font-semibold text-theme-secondary-500">Vote: <span class="text-white">:delegate</span></span>',
        'unvote_delegate'         => '<span class="font-semibold text-theme-secondary-500">Unvote: <span class="text-white">:delegate</span></span>',
        'vote_swap_delegate'      => '<span class="font-semibold text-theme-secondary-500">Unvote: <span class="text-white">:delegate_unvote</span> | Vote: <span class="text-white">:delegate_vote</span></span>',

        'types'                   => [
            'delegate-registration'               => 'Registration',
            'delegate-resignation'                => 'Resignation',
            'delegate-entity-registration'        => 'Delegate Entity Registration',
            'delegate-entity-resignation'         => 'Delegate Entity Resignation',
            'delegate-entity-update'              => 'Delegate Entity Update',
            'bridgechain-entity-registration'     => 'Bridgechain Registration',
            'bridgechain-entity-resignation'      => 'Bridgechain Resignation',
            'bridgechain-entity-update'           => 'Bridgechain Update',
            'business-entity-registration'        => 'Business Registration',
            'business-entity-resignation'         => 'Business Resignation',
            'business-entity-update'              => 'Business Update',
            'legacy-business-registration'        => 'Legacy Business Registration',
            'legacy-business-resignation'         => 'Legacy Business Resignation',
            'legacy-business-update'              => 'Legacy Business Update',
            'ipfs'                                => 'IPFS',
            'multi-payment'                       => 'Multipayment',
            'module-entity-registration'          => 'Module Registration',
            'module-entity-resignation'           => 'Module Resignation',
            'module-entity-update'                => 'Module Update',
            'vote-combination'                    => 'Vote Swap',
            'multi-signature'                     => 'Multisignature',
            'plugin-entity-registration'          => 'Plugin Registration',
            'plugin-entity-resignation'           => 'Plugin Resignation',
            'plugin-entity-update'                => 'Plugin Update',
            'product-entity-registration'         => 'Product Registration',
            'product-entity-resignation'          => 'Product Resignation',
            'product-entity-update'               => 'Product Update',
            'second-signature'                    => '2nd Signature',
            'timelock'                            => 'Timelock',
            'timelock-claim'                      => 'Timelock Claim',
            'timelock-refund'                     => 'Timelock Refund',
            'transfer'                            => 'Transfer',
            'unvote'                              => 'Unvote',
            'vote'                                => 'Vote',
            'unknown'                             => 'Unknown',
            'legacy'                              => 'Legacy',
        ],
    ],

    'wallet'                  => [
        'rank'             => 'Rank',
        'address'          => 'Address',
        'info'             => 'Info',
        'balance'          => 'Balance',
        'balance_currency' => 'Balance (:currency)',
        'supply'           => 'Supply',
        'name'             => 'Name',
        'type'             => 'Type',
        'voting'           => 'Voting',
        'percentage'       => 'Percentage',
    ],

    'delegates'               => [
        'id'           => 'ID',
        'rank'         => 'Rank',
        'name'         => 'Delegate Name',
        'status'       => 'Status',
        'votes'        => 'Votes',
        'profile'      => 'Profile',
        'commission'   => 'Commission',
        'productivity' => 'Productivity',

        'forging-status' => [
            'active'   => 'Active',
            'standby'  => 'Standby',
            'resigned' => 'Resigned',
        ],
    ],

    'exchange'                  => [
        'name'      => 'Name',
        'top_pairs' => 'Top Pairs',
        'price'     => 'Price',
        'volume'    => 'Volume',
    ],

    'urls'                    => [
        'ardent'    => 'https://ardenthq.com',
        'coingecko' => 'https://www.coingecko.com/',
    ],

    'search' => [
        'address'              => 'Address',
        'block'                => 'Block',
        'transaction'          => 'Tx',
        'balance'              => 'Balance',
        'generated_by'         => 'Generated By',
        'transactions'         => 'Transactions',
        'from'                 => 'From',
        'to'                   => 'To',
        'amount'               => 'Amount',
        'type'                 => 'Type',
        'vote'                 => 'Vote',
        'unvote'               => 'Unvote',
        'results_will_show_up' => 'Results will show up here.',
        'contract'             => 'Contract',
        'value_currency'       => 'Value (:currency)',
        'balance_currency'     => 'Balance (:currency)',
        'no_results'           => 'We could not find anything matching your search criteria, please try again!',
    ],

    'export' => [
        'information_text' => 'The data is being prepared. This might take a while, please wait.',
        'warning_text'     => 'No :type matching the selected parameters could be found.',
        'date_from'        => 'Date From',
        'date_to'          => 'Date To',

        'partial' => [
            'click'       => 'Click',
            'here'        => 'here',
            'to_download' => 'to download a partial export.',
        ],
    ],

    'time' => [
        'minutes_short'       => '~ :minutes min',
        'hours_short'         => '~ :hoursh',
        'hours_minutes_short' => '~ :hoursh :minutes min',
        'more_than_a_day'     => 'more than a day',
    ],
];
