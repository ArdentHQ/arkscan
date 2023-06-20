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
    ],

    'wallets' => [
        'balance_currency' => 'Balance (:currency)',
    ],

    'exchanges' => [
        'price'  => 'Price (:currency)',
        'volume' => 'Volume (:currency)',
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
