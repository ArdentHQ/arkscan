<?php

declare(strict_types=1);

return [

    'search' => [
        'block'                          => 'Block',
        'transaction'                    => 'Transaction',
        'wallet'                         => 'Wallet',
        'username'                       => 'Username',
        'vote'                           => 'Voting for',
        'balance_range'                  => 'Balance Range',
        'height_range'                   => 'Height Range',
        'amount_range'                   => 'Amount Range',
        'date_range'                     => 'Date Range',
        'fee_range'                      => 'Fee Range',
        'total_amount_range'             => 'Total Amount Range',
        'total_fee_range'                => 'Total Fee Range',
        'reward_range'                   => 'Reward Range',
        'smartbridge'                    => 'Smartbridge',
        'smartbridge_placeholder'        => 'Enter the Smartbridge that was in the transaction (optional)',
        'term_placeholder'               => 'Find a block, transaction, address or delegate ...',
        'term_placeholder_mobile'        => 'Search ...',
        'type'                           => 'Search Type',
        'transaction_type'               => 'Transaction Type',
        'transaction_types'              => [
            'all'                           => 'All',
            'businessEntityRegistration'    => 'Business Registration',
            'businessEntityResignation'     => 'Business Resignation',
            'businessEntityUpdate'          => 'Business Update',
            'delegateEntityRegistration'    => 'Delegate Entity Registration',
            'delegateEntityResignation'     => 'Delegate Entity Resignation',
            'delegateEntityUpdate'          => 'Delegate Entity Update',
            'delegateRegistration'          => 'Delegate Registration',
            'delegateResignation'           => 'Delegate Resignation',
            'entityRegistration'            => 'Entity Registration',
            'entityResignation'             => 'Entity Resignation',
            'entityUpdate'                  => 'Entity Update',
            'ipfs'                          => 'IPFS',
            'legacyBridgechainRegistration' => 'Bridgechain Registration',
            'legacyBridgechainResignation'  => 'Bridgechain Resignation',
            'legacyBridgechainUpdate'       => 'Bridgechain Update',
            'legacyBusinessRegistration'    => 'Legacy Business Registration',
            'legacyBusinessResignation'     => 'Legacy Business Resignation',
            'legacyBusinessUpdate'          => 'Legacy Business Update',
            'moduleEntityRegistration'      => 'Module Registration',
            'moduleEntityResignation'       => 'Module Resignation',
            'moduleEntityUpdate'            => 'Module Update',
            'multiPayment'                  => 'Multipayment',
            'multiSignature'                => 'Multisignature',
            'pluginEntityRegistration'      => 'Plugin Registration',
            'pluginEntityResignation'       => 'Plugin Resignation',
            'pluginEntityUpdate'            => 'Plugin Update',
            'productEntityRegistration'     => 'Product Registration',
            'productEntityResignation'      => 'Product Resignation',
            'productEntityUpdate'           => 'Product Update',
            'secondSignature'               => 'Second Signature',
            'timelockClaim'                 => 'Timelock Claim',
            'timelockRefund'                => 'Timelock Refund',
            'timelock'                      => 'Timelock',
            'transfer'                      => 'Transfer',
            'vote'                          => 'Vote',
            'voteCombination'               => 'Multivote',
        ],

    ],

    'settings' => [
        'currency' => [
            'title'       => 'Currency',
            'description' => 'Select display currency',
        ],
        'price_chart' => [
            'title'       => 'Price Chart',
            'description' => 'Enable/Disable price chart',
        ],
        'fee_chart' => [
            'title'       => 'Fee Chart',
            'description' => 'Enable/Disable fee chart',
        ],
        'theme' => [
            'title'       => 'Dark Theme',
            'description' => 'Enable/Disable dark theme',
        ],
        'table' => [
            'title'       => 'Compact Table',
            'description' => 'Reduce the size of tables',
        ],
    ],

];
