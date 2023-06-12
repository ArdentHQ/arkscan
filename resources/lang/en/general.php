<?php

declare(strict_types=1);

return [
    'ardent'                  => 'Ardent',
    'address'                 => 'Address',
    'beta_uppercase'          => 'BETA',
    'optional'                => 'Optional',
    'or'                      => 'or',
    'arkscan'                 => 'ARKScan',
    'scan'                    => 'Scan',
    'height'                  => 'Height',
    'network'                 => 'Network',
    'current_supply'          => 'Current Supply',
    'not_available'           => 'Not Available',
    'price'                   => 'Price',
    'market_cap'              => 'Market Cap',
    'verified_address'        => 'Verified Address',
    'exchange'                => 'Exchange',
    'reload'                  => 'Reload',
    'confirmed'               => 'Confirmed',
    'see_all'                 => 'See all',
    'wallet_not_found'        => '<span class="bg-theme-warning-100">:0</span> has no balance. <br/> <span class="text-base font-normal">Return to this page after the address has received a transaction.</span>',
    'fiat_excluding_itself'   => 'Excluding :amount sent to itself',
    'more_details'            => 'For more :transactionType details',
    'learn_more'              => 'Learn more',
    'confirmations'           => ':count confirmation|:count confirmations',
    'market_data_by'          => 'Market Data by',
    'arkvault'                => 'ARKVault',
    'select_theme'            => 'Select Theme',
    'select_network'          => 'Select Network',
    'na'                      => 'N/A',
    'all'                     => 'All',
    'overview'                => 'Overview',
    'view'                    => 'View',

    'navbar' => [
        'search_placeholder' => 'Address / Tx ID / Block ID',
        'live_network'       => 'Live Network',
        'test_network'       => 'Test Network',
        'live'               => 'Live',
        'test'               => 'Test',
        'price'              => 'Price',
        'no_results'         => 'We could not find anything matching your search criteria, please try again!',
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
        'address'                 => 'Address',
        'recipients'              => 'Recipients',
        'amount'                  => 'Amount',
        'fee'                     => 'Fee',
        'confirmations'           => 'Confirmations',
        'block_id'                => 'Block ID',
        'smartbridge'             => 'SmartBridge',
        'nonce'                   => 'Nonce',
        'multi_signature_address' => 'Multisignature Address',
        'participant'             => 'Participant #:0',
        'ipfs-hash'               => 'IPFS Hash',
        'well-confirmed'          => 'Well Confirmed',
        'delegate'                => 'Delegate',

        'types'                   => [
            'delegate-registration'               => 'Delegate Registration',
            'delegate-resignation'                => 'Delegate Resignation',
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
            'vote-combination'                    => 'Switch Vote',
            'multi-signature'                     => 'Multisignature',
            'plugin-entity-registration'          => 'Plugin Registration',
            'plugin-entity-resignation'           => 'Plugin Resignation',
            'plugin-entity-update'                => 'Plugin Update',
            'product-entity-registration'         => 'Product Registration',
            'product-entity-resignation'          => 'Product Resignation',
            'product-entity-update'               => 'Product Update',
            'second-signature'                    => 'Second Signature',
            'timelock'                            => 'Timelock',
            'timelock-claim'                      => 'Timelock Claim',
            'timelock-refund'                     => 'Timelock Refund',
            'transfer'                            => 'Transfer',
            'unvote'                              => 'Unvote',
            'vote'                                => 'Vote',
            'unknown'                             => 'Unknown',
            'other'                               => 'Other',

            // TODO: remove once legacy magistrate shows proper type texts
            'business'                            => 'Business',
            'bridgechain'                         => 'Bridgechain',
        ],
    ],

    'wallet'                  => [
        'rank'       => 'Rank',
        'address'    => 'Address',
        'info'       => 'Info',
        'balance'    => 'Balance',
        'supply'     => 'Supply',
        'name'       => 'Name',
        'type'       => 'Type',
        'voting'     => 'Voting',
        'percentage' => 'Percentage',
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
        'results_will_show_up' => 'Results will show up here',
    ],
];
