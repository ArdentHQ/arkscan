<?php

declare(strict_types=1);

return [
    'transactions' => [
        'id'         => 'Tx ID',
        'age'        => 'Age',
        'type'       => 'Type',
        'addressing' => 'Addressing',
        'amount'     => 'Amount (:currency)',
        'fee'        => 'Fee (:currency)',
        'to'         => 'To',
        'from'       => 'From',
        'contract'   => 'Contract',
        'multiple'   => 'Multiple',
        'address'    => 'Address',

        'amount_no_currency' => 'Amount',

        'no_results' => [
            'no_filters'            => 'All filtering options have been deselected. Please select one or more options to display transactions.',
            'no_addressing_filters' => 'Addressing options are currently unselected. Please choose at least one option to display outgoing and/or incoming transactions.',
            'no_results'            => 'No transactions matching the selected types could be found.',
        ],
    ],

    'blocks' => [
        'height'       => 'Block Height',
        'age'          => 'Age',
        'transactions' => 'Transactions',
        'volume'       => 'Volume (:currency)',
        'total_reward' => 'Total Reward (:currency)',
        'value'        => 'Value (:currency)',
        'no_results'   => 'This delegate has not yet validated a block.',
    ],

    'wallets' => [
        'balance_currency' => 'Balance (:currency)',
        'no_results'       => 'This delegate does not currently have any voters.',
    ],

    'exchanges' => [
        'price'  => 'Price (:currency)',
        'volume' => 'Volume (:currency)',
    ],

    'delegates' => [
        'rank'          => 'Rank',
        'delegate'      => 'Delegate',
        'status'        => 'Status',
        'no_of_voters'  => '# of Voters',
        'votes'         => 'Votes (:currency)',
        'percentage'    => 'Percentage',
        'missed_blocks' => 'Missed Blocks (30d)',

        'delegate_per_page_options' => [
            10,
            25,
            51,
            100,
        ],

        'info' => [
            'percentage' => 'Percentage of votes in relation to the total supply.',
        ],

        'no_results' => [
            'no_filters' => 'All filtering options have been deselected. Please select one or more options to display transactions.',
            'no_results' => 'No delegates matching the selected types could be found.',
        ],
    ],

    'filters' => [
        'transactions' => [
            'addressing'    => 'Addressing',
            'types'         => 'Types',
            'select_all'    => 'Select All',
            'outgoing'      => 'Outgoing',
            'incoming'      => 'Incoming',
            'to'            => 'To',
            'from'          => 'From',
            'transfers'     => 'Transfers',
            'votes'         => 'Votes',
            'multipayments' => 'Multipayments',
            'others'        => 'Others',
        ],
    ],
];
