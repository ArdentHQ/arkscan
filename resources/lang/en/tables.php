<?php

declare(strict_types=1);

return [
    'home' => [
        'transactions' => 'Transactions',
        'blocks'       => 'Blocks',
    ],

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
        'return'     => 'Return',

        'amount_no_currency' => 'Amount',

        'no_results' => [
            'no_filters'            => 'All filtering options have been deselected. Please select one or more options to display transactions.',
            'no_addressing_filters' => 'Addressing options are currently unselected. Please choose at least one option to display outgoing and/or incoming transactions.',
            'no_results'            => 'No transactions matching the selected types could be found.',
        ],
    ],

    'blocks' => [
        'height'       => 'Block Height',
        'generated_by' => 'Generated By',
        'age'          => 'Age',
        'transactions' => 'Transactions',
        'volume'       => 'Volume (:currency)',
        'total_reward' => 'Total Reward (:currency)',
        'value'        => 'Value (:currency)',
        'no_results'   => 'There are currently no blocks.',
    ],

    'wallet' => [
        'blocks' => [
            'no_results' => 'This validator has not yet validated a block.',
        ],
    ],

    'wallets' => [
        'balance_currency' => 'Balance (:currency)',
        'no_results'       => 'This validator does not currently have any voters.',
    ],

    'exchanges' => [
        'name'            => 'Name',
        'top_pairs'       => 'Top Pairs',
        'price'           => 'Price',
        'volume'          => 'Volume',
        'price_currency'  => 'Price (:currency)',
        'volume_currency' => 'Volume (:currency)',
    ],

    'validators' => [
        'rank'          => 'Rank',
        'validator'      => 'Validator',
        'status'        => 'Status',
        'no_of_voters'  => '# of Voters',
        'votes'         => 'Votes (:currency)',
        'percentage'    => 'Percentage',
        'missed_blocks' => 'Missed Blocks (30d)',

        'validator_per_page_options' => [
            10,
            25,
            53,
            100,
        ],

        'info' => [
            'percentage' => 'Percentage of votes in relation to the total supply.',
        ],

        'no_results' => [
            'no_filters' => 'All filtering options have been deselected. Please select one or more options to display transactions.',
            'no_results' => 'No validators matching the selected types could be found.',
        ],
    ],

    'validator-monitor' => [
        'order'                => 'Order',
        'validator'             => 'Validator',
        'status'               => 'Status',
        'time_to_forge'        => 'Time to Forge',
        'status_time_to_forge' => 'Status / Time to Forge',
        'block_height'         => 'Block Height',
        'completed'            => 'Completed',
        'missed'               => 'Missed',
        'tbd'                  => 'TBD',
        'favorite'             => 'Favorite',
        'my_favorites'         => 'My Favorites',

        'forging-status' => [
            'block_generated' => 'Block Generated',
            'generating'      => 'Generating ...',
            'pending'         => 'Pending',
            'blocks_missed'   => ':count Blocks Missed',
        ],
    ],

    'missed-blocks' => [
        'height'        => 'Block Height',
        'age'           => 'Age',
        'validator'      => 'Validator',
        'no_of_voters'  => '# of Voters',
        'votes'         => 'Votes (:currency)',
        'percentage'    => 'Percentage',
        'addressing'    => 'Addressing',

        'info' => [
            'percentage' => 'Percentage of votes in relation to the total supply.',
        ],

        'no_results' => 'There have been no missed blocks within the past 30 days.',
    ],

    'recent-votes' => [
        'id'         => 'Tx ID',
        'age'        => 'Age',
        'type'       => 'Type',
        'addressing' => 'Addressing',
        'from'       => 'From',
        'validator'   => 'Validator',

        'no_results' => [
            'no_filters' => 'All filtering options have been deselected. Please select one or more options to display recent votes.',
            'no_results' => 'No matches for the selected type could be found.',
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

        'validators' => [
            'select_all' => 'Select All',
            'active'     => 'Active',
            'standby'    => 'Standby',
            'resigned'   => 'Resigned',
        ],

        'recent-votes' => [
            'select_all' => 'Select All',
            'vote'       => 'Vote',
            'unvote'     => 'Unvote',
            'vote_swap'  => 'Vote Swap',
        ],
    ],
];
