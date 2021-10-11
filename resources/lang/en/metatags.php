<?php

declare(strict_types=1);

return [

    'home' => [
        'title'       => ':name Blockchain Explorer',
        'description' => 'View transactions, blocks, nodes, and other network activity on the :name Blockchain.',
        'image'       => asset('images/metadata/homepage.png'),
    ],

    'statistics' => [
        'title'       => 'Statistics & Analytics - :name Blockchain Explorer',
        'description' => 'View statistics and analyze network activity on the :name Blockchain.',
        'image'       => asset('images/metadata/statistics.png'),
    ],

    'delegates' => [
        'title'       => 'Delegates - :name Blockchain Explorer',
        'description' => 'View Delegates (Validators) and their activity on the :name Blockchain.',
        'image'       => asset('images/metadata/delegates.png'),
    ],

    'transactions' => [
        'title'       => 'Transactions - :name Blockchain Explorer',
        'description' => 'View transaction details from the :name Blockchain.',
        'image'       => asset('images/metadata/transactions.png'),
    ],

    'transaction' => [
        'title'       => 'Transaction :txid Details - :name Blockchain Explorer',
        'description' => 'View information and details for transaction with ID :txid on the :name Blockchain.',
        'image'       => asset('images/metadata/transactions.png'),
    ],

    'blocks' => [
        'title'       => 'Blocks - :name Blockchain Explorer',
        'description' => 'View details for blocks on the :name Blockchain.',
        'image'       => asset('images/metadata/blocks.png'),
    ],

    'block' => [
        'title'       => 'Block :blockid Details - :name Blockchain Explorer',
        'description' => 'View information and details for block with ID :blockid on the :name Blockchain.',
        'image'       => asset('images/metadata/blocks.png'),
    ],

    'wallets' => [
        'title'       => 'Wallets - :name Blockchain Explorer',
        'description' => 'View wallet address details on the :name Blockchain.',
        'image'       => asset('images/metadata/wallets.png'),
    ],

    'wallet' => [
        'title'       => 'Wallet :address Details - :name Blockchain Explorer',
        'description' => 'View balance, transaction history, and other details for :address on the :name Blockchain.',
        'image'       => asset('images/metadata/wallets.png'),
    ],

    'wallet-voters' => [
        'title'       => 'Voters of :delegate - :name Blockchain Explorer',
        'description' => 'View all voters of :delegate on the :name Blockchain.',
        'image'       => asset('images/metadata/wallets.png'),
    ],

    'wallet-blocks' => [
        'title'       => 'Blocks Validated by :delegate - :name Blockchain Explorer',
        'description' => 'View blocks validated by :delegate on the :name Blockchain.',
        'image'       => asset('images/metadata/wallets.png'),
    ],

    'search' => [
        'title'       => 'Search Results for :searchTerm - :name Blockchain Explorer',
        'description' => 'View search results for :searchTerm on the :name Blockchain.',
        'image'       => asset('images/metadata/search.png'),
    ],

    '404' => [
        'title'       => '404 - Error | :name Blockchain Explorer',
        'description' => '',
        'image'       => asset('images/metadata/homepage.png'),
    ],
];
