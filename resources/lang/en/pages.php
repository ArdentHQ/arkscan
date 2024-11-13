<?php

declare(strict_types=1);

return [

    'block'            => [
        'title'                 => 'Block Details',
        'block_id'              => 'Block ID',
        'generated_by'          => 'Generated By',
        'transaction_volume'    => 'Transaction Volume',
        'transactions'          => 'Transactions',
        'total_rewards'         => 'Total Rewards',
        'total_rewards_tooltip' => 'Includes :0 Block Reward',
        'block_id_copied'       => 'Block ID Copied',

        'block_details' => 'Block Details',
        'transactions'  => 'Transactions',
        'block_summary' => 'Block Summary',
        'generated_by'  => 'Generated By',

        'header' => [
            'timestamp'     => 'Timestamp',
            'height'        => 'Height',
            'transactions'  => 'Transactions',
            'volume'        => 'Volume',
            'block_reward'  => 'Block Reward',
            'total_fees'    => 'Total Fees',
            'validator'     => 'Validator',
        ],
    ],

    'home'             => [
        'statistics' => [
            'title'               => 'Statistics',
            'current_supply'      => 'Current Supply',
            'volume'              => 'Volume (24h)',
            'market_cap'          => 'Market Cap',
            'block_height'        => 'Block Height',
            'currency_price'      => ':currency Price',
            'chart_not_supported' => 'Not supported on development networks',
        ],

        'footer' => [
            'title'    => 'Manage Your $ARK',
            'subtitle' => 'Available on Desktop & Mobile',
        ],

        'charts'                  => [
            'price'     => 'Price',
            'min_price' => 'Min Price',
            'max_price' => 'Max Price',
            'avg_price' => 'Avg Price',

            'fees'      => 'Fees',
            'min_fees'  => 'Min Fees',
            'max_fees'  => 'Max Fees',
            'avg_fees'  => 'Avg Fees',

            'periods'   => [
                'all'       => 'All Time',
                'day'       => 'Day',
                'week'      => 'Week',
                'month'     => 'Month',
                'quarter'   => 'Quarter',
                'year'      => 'Year',
            ],
        ],

        'network-details'         => [
            'price'                        => 'Price',
            'lifetime_transactions_volume' => 'Lifetime Transaction Volume',
            'lifetime_transactions'        => 'Lifetime Transactions',
            'total_votes'                  => 'Total Votes',
        ],

        'no_transaction_results'  => 'No :0 transactions could be found.',
        'transactions_and_blocks' => 'Transaction & Blocks',
        'transactions'            => 'Transactions',
        'blocks'                  => 'Blocks',
    ],

    'search_results'   => [
        'title'      => 'Results',
        'no_results' => 'We could not find anything matching your search criteria, please try again!',
    ],

    'transaction'      => [
        'action'                => 'Action',
        'transaction_id'        => 'Transaction ID',
        'transaction_id_copied' => 'Transaction ID Copied',
        'transaction_details'   => 'Transaction Details',
        'addressing'            => 'Addressing',
        'memo'                  => 'Memo (SmartBridge)',
        'recipients_list'       => 'Recipients List',
        'participants'          => 'Participants',
        'transaction_summary'   => 'Transaction Summary',
        'more_details'          => 'More Details',
        'gas_information'       => 'Gas Information',
        'other_attributes'      => 'Other Attributes',
        'input_data'            => 'Input Data',

        'header' => [
            'timestamp'            => 'Timestamp',
            'block'                => 'Block',
            'nonce'                => 'Nonce',
            'method'               => 'Method',
            'validator'            => 'Validator',
            'username'             => 'Name',
            'old_validator'        => 'Old Validator',
            'new_validator'        => 'New Validator',
            'address'              => 'Address',
            'signatures'           => 'Signatures',
            'hash'                 => 'Hash',
            'from'                 => 'From',
            'to'                   => 'To',
            'amount'               => 'Amount',
            'fee'                  => 'Fee',
            'value'                => 'Value',
            'gas_limit'            => 'Gas Limit',
            'usage_by_transaction' => 'Usage By Txn',
            'position_in_block'    => 'Position In Block',
        ],

        'value' => [
            'multiple_x' => 'Multiple (<button type="link" data-link-scroll-to="#recipients-list" class="link">:count</button>)',
        ],

        'code-block' => [
            'copy_code' => 'Copy Code',

            'tab' => [
                'default'  => 'Default',
                'utf-8'    => 'UTF-8',
                'original' => 'Original',
            ],
        ],
    ],

    'transactions'     => [
        'title'            => 'Transactions',
        'subtitle'         => 'List of transactions on the :network',
        'transactions_24h' => 'Transactions (24h)',
        'volume_24h'       => 'Transaction Volume (24h)',
        'total_fees_24h'   => 'Total Fees (24h)',
        'average_fee_24h'  => 'Average Fee (24h)',
    ],

    'wallets'          => [
        'title'              => 'Top Accounts',
        'subtitle'           => 'A list of the largest ARK addresses by token holdings.',
        'supply_tooltip'     => 'Percentage of the total :symbol supply',
        'percentage_tooltip' => 'Percentage of votes in relation to the validator total.',

        'blocks' => [
            'volume_tooltip'       => 'A sum of transactions value in the block.',
            'total_reward_tooltip' => 'A sum of the block reward and transaction fees.',
            'value_tooltip'        => 'Value of rewards in user defined currency.',
        ],
    ],

    'wallet'           => [
        'title'                  => 'Address Details',
        'address'                => 'Address',
        'generated_by'           => 'Generated by',
        'all_transactions'       => 'All History',
        'received_transactions'  => 'Incoming',
        'sent_transactions'      => 'Outgoing',
        'voting_for'             => 'Voting For',
        'rank'                   => 'Rank',
        'commission'             => 'Commission',
        'balance'                => 'Balance',
        'amount'                 => 'Amount',
        'smartbridge'            => 'Smartbridge',
        'vote_rank'              => '#:0',
        'status'                 => 'Status',
        'productivity'           => 'Productivity',
        'productivity_tooltip'   => 'Success rate of validating blocks over the last 30 days.',
        'no_results'             => 'No :0 transactions could be found.',
        'transactions'           => 'Transactions',
        'validator_info'         => 'Validator Info',
        'name'                   => 'Name',
        'value'                  => 'Value',
        'copied_public_key'      => 'Public Key Copied',
        'address_copied'         => 'Address Copied',

        'qrcode'                => [
            'title'                             => 'Send Funds',
            'description'                       => 'Enter the amount you wish to transfer to this address and confirm.',
            'automatic_notice'                  => 'The QR-code is updated automatically, you do not need to press anything.',
            'or_send_with'                      => 'or send with',
            'specify_amount'                    => 'Specify Amount',
            'send_from_wallet'                  => 'Send From Wallet',
            'currency_amount'                   => ':currency Amount',
            'memo_optional'                     => 'Memo (optional)',
            'arkconnect_specify_amount_tooltip' => 'Specify amount to send with ARK Connect',
        ],

        'public_key'            => [
            'title'       => 'Public Key',
        ],

        'validator'              => [
            'title'                 => 'Validator :0',
            'rank'                  => 'Rank',
            'status'                => 'Status',
            'commission'            => 'Commission',
            'payout_frequency'      => 'Payout Frequency',
            'payout_minimum'        => 'Payout Minimum',
            'forged_total'          => 'Total Forged',
            'votes'                 => 'Votes (:0)',
            'votes_percentage'      => 'Votes (:0%)',
            'forged_blocks'         => 'Forged Blocks',
            'productivity'          => 'Productivity (30 Days)',
            'voters'                => 'Voters',
            'resigned'              => 'Resigned',
            'validated_blocks'      => 'Validated Blocks',
            'votes_title'           => 'Votes',
            'productivity_title'    => 'Productivity',
            'not_registered_text'   => 'This address is not a registered validator',
            'vote_for_validator'    => 'Vote for Validator',
            'unvote_validator'      => 'Unvote Validator',
            'resigned_vote_tooltip' => 'Cannot vote for resigned validators.',
        ],

        'export-transactions-modal' => [
            'title'       => 'Export Table',
            'description' => 'Select the data below that you want to export.',

            'date_range'          => 'Date Range',
            'delimiter'           => 'Delimiter',
            'types'               => 'Types',
            'columns'             => 'Columns',
            'include_header_row'  => 'Include Header Row',
            'types_placeholder'   => 'Select Types',
            'columns_placeholder' => 'Select Columns',
            'success_toast'       => '<span class="font-semibold" style="word-break: break-word;">:address.csv</span> has been saved successfully',

            'types_x_selected' => [
                'singular' => 'Type Selected',
                'plural'   => 'Types Selected',
            ],

            'columns_x_selected' => [
                'singular' => 'Column Selected',
                'plural'   => 'Columns Selected',
            ],

            'date-options' => [
                'current_month' => 'Current Month',
                'last_month'    => 'Last Month',
                'last_quarter'  => 'Last Quarter',
                'current_year'  => 'Current Year',
                'last_year'     => 'Last Year',
                'all'           => 'All',
            ],

            'delimiter-options' => [
                'comma'     => [
                    'text'  => 'Comma',
                    'value' => ',',
                ],
                'semicolon' => [
                    'text'  => 'Semicolon',
                    'value' => ';',
                ],
                'tab'       => [
                    'text'  => 'Tab',
                    'value' => '\\t',
                ],
                'pipe'      => [
                    'text'  => 'Pipe',
                    'value' => '|',
                ],
            ],

            'types-options' => [
                'transfers'     => 'Transfers',
                'votes'         => 'Votes',
                'multipayments' => 'Multipayments',
                'others'        => 'Others',
            ],

            'columns-options' => [
                'id'         => 'Transaction ID',
                'timestamp'  => 'Transaction Date',
                'sender'     => 'Addressing (From)',
                'recipient'  => 'Addressing (To)',
                'amount'     => 'Value [:networkCurrency]',
                'amountFiat' => 'Value [:userCurrency]',
                'fee'        => 'Fee [:networkCurrency]',
                'feeFiat'    => 'Fee [:userCurrency]',
                'rate'       => 'Rate [:userCurrency]',
            ],
        ],

        'export-blocks-modal' => [
            'title'       => 'Export Table',
            'description' => 'Select the data below that you want to export.',

            'date_range'          => 'Date Range',
            'delimiter'           => 'Delimiter',
            'types'               => 'Types',
            'columns'             => 'Columns',
            'include_header_row'  => 'Include Header Row',
            'types_placeholder'   => 'Select Types',
            'columns_placeholder' => 'Select Columns',
            'success_toast'       => '<span class=\\\'font-semibold\\\'>:username.csv</span> has been saved successfully',

            'columns_x_selected' => [
                'singular' => 'Column Selected',
                'plural'   => 'Columns Selected',
            ],

            'date-options' => [
                'current_month' => 'Current Month',
                'last_month'    => 'Last Month',
                'last_quarter'  => 'Last Quarter',
                'current_year'  => 'Current Year',
                'last_year'     => 'Last Year',
                'all'           => 'All',
            ],

            'delimiter-options' => [
                'comma'     => [
                    'text'  => 'Comma',
                    'value' => ',',
                ],
                'semicolon' => [
                    'text'  => 'Semicolon',
                    'value' => ';',
                ],
                'tab'       => [
                    'text'  => 'Tab',
                    'value' => '\\t',
                ],
                'pipe'      => [
                    'text'  => 'Pipe',
                    'value' => '|',
                ],
            ],

            'columns-options' => [
                'id'                   => 'Block ID',
                'timestamp'            => 'Block Date',
                'numberOfTransactions' => 'Transactions',
                'volume'               => 'Volume [:networkCurrency]',
                'volumeFiat'           => 'Volume [:userCurrency]',
                'total'                => 'Total Rewards [:networkCurrency]',
                'totalFiat'            => 'Total Rewards [:userCurrency]',
                'rate'                 => 'Rate [:userCurrency]',
            ],
        ],
    ],

    'validators'        => [
        'title'               => 'Validators',
        'subtitle'            => 'List of validators registered on the network.',
        'x_validators'        => '{1} 1 Validator|:count Validators',
        'voting_x_addresses'  => 'Voting (:count Addresses)',

        'missed-blocks'      => [
            'title'          => 'Missed Blocks (30 Days)',
            'results_suffix' => '(30 Days)',
        ],

        'recent-votes'      => [
            'results_suffix' => '(30 Days)',
        ],

        'explore'            => [
            'title'    => 'How can I become a validator?',
            'subtitle' => 'Step-by-step guide on how to register as a validator',
            'action'   => 'Explore',
        ],

        'tabs'               => [
            'validators'     => 'Validators',
            'missed_blocks'  => 'Missed Blocks',
            'recent_votes'   => 'Recent Votes',
        ],

        'active'             => 'Active',
        'standby'            => 'Standby',
        'resigned'           => 'Resigned',
        'order'              => 'Order',
        'name'               => 'Validator Name',
        'forging_at'         => 'Time to Forge',
        'status'             => 'Status',
        'block_id'           => 'Block ID',
        'success'            => 'Block Generated',
        'warning'            => 'Block Missed',
        'danger'             => ':0 Blocks Missed',
        'completed'          => 'Completed',
        'next'               => 'Next',
        'now'                => 'Now',
        'monitor'            => 'Monitor',

        'arkconnect'         => [
            'voting_for_tooltip'     => 'You\'re voting for this validator',
            'recommend_switch_votes' => 'We recommend switching your vote to an active validator.',
        ],

        'statistics' => [
            'validator_registrations' => 'Validator Registrations',
            'block_reward'            => 'Block Reward',
            'fees_collected'          => 'Fees Collected (24h)',
            'votes'                   => 'Current Votes',
            'block_count'             => 'Current Round',
            'transactions'            => 'Round Transactions',
            'current_validator'       => 'Current',
            'next_validator'          => 'Next',
            'next_slot'               => 'Next Slot',
            'blocks_generated'        => ':forged / :total Blocks',
            'forging'                 => 'Forging',
            'missed'                  => 'Missed',
            'not_forging'             => 'Not Forging',
        ],
        'info'       => [
            'status'       => 'View the status of the last 5 blocks for a validator. Latest blocks are displayed from right to left.',
            'productivity' => 'The productivity statistic is calculated over the previous 30 day period.',
        ],
    ],

    'validator-monitor' => [
        'title'                 => 'Validator Monitor',
        'subtitle'              => 'Validator block production observer tool.',
        'missed_blocks_tooltip' => 'Validator last forged :blocks blocks ago (:time)',

        'stats' => [
            'forging'        => 'Forging',
            'missed'         => 'Missed',
            'not_forging'    => 'Not Forging',
            'current_height' => 'Current Height',
            'current_round'  => 'Current Round',
            'next_slot'      => 'Next Slot',
        ],
    ],

    'blocks_by_wallet' => [
        'title'       => 'Generated Blocks',
        'table_title' => 'Block History',
        'no_results'  => 'This Validator has not generated any blocks yet. Generated blocks will appear in a list here.',
    ],

    'voters_by_wallet' => [
        'title'      => 'Validator Voters',
        'subtitle'   => 'Voters',
        'no_results' => 'This Validator does not have any voters yet. Voters will appear in a list here.',
    ],

    'blocks'           => [
        'title'               => 'Blocks',
        'subtitle'            => 'List of blocks on the :network',
        'blocks_produced_24h' => 'Blocks Produced (24h)',
        'missed_blocks_24h'   => 'Missed Blocks (24h)',
        'block_rewards_24h'   => 'Block Rewards (24h)',
        'largest_block_24h'   => 'Largest Block (24h)',
    ],

    'statistics'       => [
        'title'     => 'Statistics',
        'subtitle'  => 'Explore information on blockchain data and fees categorized by type.',

        'highlights' => [
            'total_supply'  => 'Total Supply',
            'voting'        => 'Voting (:percent)',
            'validators'    => 'Validators',
            'addresses'     => 'Addresses',
        ],

        'information-cards'   => [
            'all-time-transactions'   => 'All-Time Transactions',
            'transactions'            => 'Transactions',
            'current-average-fee'     => 'Current Average Fee (:type)',
            'min-fee'                 => 'Min Fee',
            'max-fee'                 => 'Max Fee',
            'all-time-fees-collected' => 'All-Time Fees Collected',
            'fees'                    => 'Fees',
        ],

        'insights' => [
            'title'    => 'Insights',
            'subtitle' => 'Explore interesting on-chain and market data for ARK.',

            'dropdown' => [
                'transactions'  => 'Transaction Data',
                'market_data'   => 'Market Data',
                'validators'    => 'Validator Data',
                'addresses'     => 'Address Data',
                'annual'        => 'Annual Data',
            ],

            'transactions' => [
                'title'          => 'Transaction Data',
                'all_time'       => 'All-Time',
                'daily_averages' => 'Daily Averages',
                'records'        => 'Records',

                'header' => [
                    'transfer'                    => 'Transfers',
                    'multipayment'                => 'Multipayments',
                    'vote'                        => 'Votes',
                    'unvote'                      => 'Unvotes',
                    'validator_registration'      => 'Validator Registrations',
                    'validator_resignation'       => 'Validator Resignations',
                    'transactions'                => 'Transactions',
                    'transaction_volume'          => 'Transaction Volume',
                    'transaction_fees'            => 'Transaction Fees',
                    'largest_transaction'         => 'Largest Transaction',
                    'largest_block'               => 'Largest Block',
                    'highest_fee'                 => 'Highest Fee',
                    'most_transactions_in_block'  => 'Most Transactions in Block',
                    'transaction_id'              => 'TxID',
                    'block'                       => 'Block',
                    'date'                        => 'Date',
                    'amount'                      => 'Amount',
                    'mobile'                      => [
                        'largest_transaction'        => 'Largest Tx',
                        'largest_block'              => 'Largest Block',
                        'highest_fee'                => 'Highest Fee',
                        'most_transactions_in_block' => 'Most Tx in Block',
                    ],
                ],
            ],

            'market_data' => [
                'title'            => 'Market Data',
                'price'            => 'Price',
                'exchanges_volume' => 'Exchanges Volume',
                'market_cap'       => 'Market Cap',

                'header' => [
                    'daily'         => 'Daily Range',
                    'year'          => '52-Week Range',
                    'atl'           => 'All-Time Low',
                    'ath'           => 'All-Time High',
                    'today_volume'  => 'Today\'s Volume',
                    'today_value'   => 'Today\'s Value',
                    'date'          => 'Date',
                ],
            ],

            'addresses' => [
                'title'    => 'Address Data',
                'holdings' => 'Address Holdings',
                'unique'   => 'Unique Addresses',

                'header' => [
                    'genesis'           => 'Genesis',
                    'newest'            => 'Newest',
                    'most_transactions' => 'Most Transactions',
                    'largest'           => 'Largest',
                    'addresses'         => 'Addresses',
                    'address'           => 'Address',
                    'date'              => 'Date',
                    'transactions'      => 'Transactions',
                    'balance'           => 'Balance',
                ],
            ],

            'validators' => [
                'title'          => 'Validator Data',

                'header' => [
                    'most_unique_voters'      => 'Most Unique Voters',
                    'least_unique_voters'     => 'Least Unique Voters',
                    'oldest_active_validator' => 'Oldest Active Validator',
                    'newest_active_validator' => 'Newest Active Validator',
                    'most_blocks_forged'      => 'Most Blocks Forged (All-Time)',
                    'voters'                  => 'Voters',
                    'registered'              => 'Registered',
                    'blocks'                  => 'Blocks',
                ],
            ],

            'annual' => [
                'title'  => 'Annual Data',
                'header' => [
                    'transaction' => 'Tx',
                    'volume'      => 'Volume',
                    'fees'        => 'Fees',
                    'blocks'      => 'Blocks',
                ],
            ],
        ],
    ],

    'footer'           => [
        'all_rights_reserved' => 'All rights reserved',
        'copyright'           => 'Made with ♥ by',
    ],

    'support'          => [
        'title'          => 'Support',
        'description'    => 'We are here to help. Get the answers to your ARK Scan related questions below.',
        'let_us_help'    => [
            'description' => "Can't find what you're looking for? Many support-related questions can be answered by checking out our extensive documentation.",
        ],
        'page_name'      => 'Help & Support',
        'docs'           => 'https://arkscan.io/docs',
        'additional'     => 'Need more help? You can contact our support team and we can work on resolving your issue directly.',
        'form_submitted' => 'Thank you for submitting the contact form. We\'ll be in touch soon!',
        'form'           => [
            'title'       => 'Contact Our Support Team',
            'description' => '',
        ],
    ],

    'compatible-wallets' => [
        'title'    => 'Compatible Wallets',
        'subtitle' => 'These wallets support the ARK Token.',

        'arkvault' => [
            'disclaimer'  => 'ARK Desktop Wallet is discontinued. Please use ARKVault instead!',
            'web_wallet'  => 'Web Wallet',
            'description' => 'ARKVault is the native web-wallet solution developed by the team behind ARK Core. It was built from the ground up to be the best ARK wallet possible and we highly recommend you give it a try!',
            'subtitle'    => 'Control Your Assets',
        ],

        'arkconnect' => [
            'title'       => 'ARKConnect',
            'title_extra' => '(Browser Extension)',
            'subtitle'    => 'Web3 Browser Extension',
        ],

        'wallets' => [
            'abra' => [
                'title' => 'Abra',
                'url'   => 'https://www.abra.com/',
                'logo'  => 'abra',
            ],
            'atomic' => [
                'title' => 'Atomic Wallet',
                'url'   => 'https://atomicwallet.io/downloads',
                'logo'  => 'atomic',
            ],
            'exodus' => [
                'title' => 'Exodus',
                'url'   => 'https://www.exodus.com/download',
                'logo'  => 'exodus',
            ],
            'ledger' => [
                'title' => 'Ledger',
                'url'   => 'https://www.ledger.com/ledger-live',
                'logo'  => 'ledger',
            ],
            'crypto' => [
                'title' => 'Crypto',
                'url'   => 'https://crypto.com/app',
                'logo'  => 'crypto',
            ],
        ],

        'get_listed' => "Don't see a wallet listed? <span class=\"whitespace-nowrap\">Let us know!</span>",

        'submit-modal' => [
            'title'               => 'Submit a Listing',
            'name'                => 'Wallet Name',
            'website'             => 'Website Address',
            'website_placeholder' => 'https://website.com',
            'message'             => 'Additional Details',
            'success_toast'       => 'Thank you. Your message has been submitted.',
            'throttle_error'      => 'You have made too many requests. Please wait :time before reporting again.',
        ],
    ],

    'exchanges' => [
        'title'            => 'Exchange Listings',
        'subtitle'         => 'A listing of active exchanges and their ARK pairs.',
        'get_listed'       => "Don't see an exchange listed? <span class=\"whitespace-nowrap\">Let us know!</span>",
        'live_price_chart' => 'Live Price Chart',

        'type' => [
            'title'       => 'Type',
            'exchanges'   => 'Exchanges',
            'agreggators' => 'Agreggators',
        ],

        'pair' => [
            'title'       => 'Pair',
            'btc'         => 'BTC',
            'eth'         => 'ETH',
            'stablecoins' => 'Stablecoins',
            'other'       => 'Other',
        ],

        'submit-modal' => [
            'title'               => 'Submit a Listing',
            'name'                => 'Exchange Name',
            'website'             => 'Website Address',
            'website_placeholder' => 'https://website.com',
            'pairs'               => 'ARK Pairs',
            'pairs_placeholder'   => 'USDT/BTC/ETH...',
            'message'             => 'Additional Details',
            'success_toast'       => 'Thank you. Your message has been submitted.',
            'throttle_error'      => 'You have made too many requests. Please wait :time before reporting again.',
        ],

        'chart'      => [
            'current_price' => 'Current Price',
            'market-cap'    => 'Market Cap',
            'min-price'     => 'Min Price',
            'max-price'     => 'Max Price',
        ],
    ],
];
