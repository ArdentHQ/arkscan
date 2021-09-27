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
        'term_placeholder'        => 'Find a block, transaction, address or delegate ...',
        'term_placeholder_mobile' => 'Search ...',
        'type'                    => 'Search Type',
        'transaction_type'        => 'Transaction Type',
        'transaction_types'       => [
            'all'                  => 'All',
            'delegateRegistration' => 'Delegate Registration',
            'delegateResignation'  => 'Delegate Resignation',
            'ipfs'                 => 'IPFS',
            'multiPayment'         => 'Multipayment',
            'multiSignature'       => 'Multisignature',
            'secondSignature'      => 'Second Signature',
            'timelockClaim'        => 'Timelock Claim',
            'timelockRefund'       => 'Timelock Refund',
            'timelock'             => 'Timelock',
            'transfer'             => 'Transfer',
            'vote'                 => 'Vote',
            'voteCombination'      => 'Switch Vote',
            'magistrate'           => 'Magistrate',
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
            'title'       => 'Expanded Tables',
            'description' => 'Increases spacing of table items (desktop only)',
        ],
    ],

    'statistics' => [
        'periods' => [
            'day'     => 'Day',
            'week'    => 'Week',
            'month'   => 'Month',
            'quarter' => 'Quarter',
            'year'    => 'Year',
            'all'     => 'All',
        ],
    ],

];
