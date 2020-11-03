<?php

declare(strict_types=1);

return [

    'search' => [
        'block'                   => 'Block',
        'transaction'             => 'Transaction',
        'wallet'                  => 'Wallet',
        'username'                => 'Username',
        'vote'                    => 'Voting for',
        'balance_range'           => 'Balance Range',
        'height_range'            => 'Height Range',
        'amount_range'            => 'Amount Range',
        'date_range'              => 'Date Range',
        'fee_range'               => 'Fee Range',
        'total_amount_range'      => 'Total Amount Range',
        'total_fee_range'         => 'Total Fee Range',
        'reward_range'            => 'Reward Range',
        'smartbridge'             => 'Smartbridge',
        'smartbridge_placeholder' => 'Enter the Smartbridge that was in the transaction (optional)',
        'term_placeholder'        => 'Find a block, transaction, address or Delegate ...',
        'term_placeholder_mobile' => 'Search ...',
        'type'                    => 'Search Type',
        'transaction_type'        => 'Transaction Type',
        'transaction_types'       => [
            'all'                           => 'All',
            'businessEntityRegistration'    => 'Business Entity Registration',
            'businessEntityResignation'     => 'Business Entity Resignation',
            'businessEntityUpdate'          => 'Business Entity Update',
            'delegateEntityRegistration'    => 'Delegate Entity Registration',
            'delegateEntityResignation'     => 'Delegate Entity Resignation',
            'delegateEntityUpdate'          => 'Delegate Entity Update',
            'delegateRegistration'          => 'Delegate Registration',
            'delegateResignation'           => 'Delegate Resignation',
            'entityRegistration'            => 'Entity Registration',
            'entityResignation'             => 'Entity Resignation',
            'entityUpdate'                  => 'Entity Update',
            'ipfs'                          => 'IPFS',
            'legacyBridgechainRegistration' => 'Legacy Bridgechain Registration',
            'legacyBridgechainResignation'  => 'Legacy Bridgechain Resignation',
            'legacyBridgechainUpdate'       => 'Legacy Bridgechain Update',
            'legacyBusinessRegistration'    => 'Legacy Business Registration',
            'legacyBusinessResignation'     => 'Legacy Business Resignation',
            'legacyBusinessUpdate'          => 'Legacy Business Update',
            'moduleEntityRegistration'      => 'Module Entity Registration',
            'moduleEntityResignation'       => 'Module Entity Resignation',
            'moduleEntityUpdate'            => 'Module Entity Update',
            'multiPayment'                  => 'Multipayment',
            'multiSignature'                => 'Multisignature',
            'pluginEntityRegistration'      => 'Plugin Entity Registration',
            'pluginEntityResignation'       => 'Plugin Entity Resignation',
            'pluginEntityUpdate'            => 'Plugin Entity Update',
            'productEntityRegistration'     => 'Product Entity Registration',
            'productEntityResignation'      => 'Product Entity Resignation',
            'productEntityUpdate'           => 'Product Entity Update',
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
    ],

];
