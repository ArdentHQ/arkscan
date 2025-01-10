<?php

declare(strict_types=1);

return [

    'home'          => [
        'title'       => ':name Blockchain Explorer',
        'description' => 'View transactions, blocks, nodes, and other network activity on the :name Blockchain.',
        'image'       => 'metadata/homepage.png',
    ],

    'statistics'    => [
        'title'       => 'Statistics & Analytics | :name Blockchain Explorer',
        'description' => 'View statistics and analyze network activity on the :name Blockchain.',
        'image'       => 'metadata/statistics.png',
    ],

    'validators'     => [
        'title'       => 'Validators | :name Blockchain Explorer',
        'description' => 'View Validators and their activity on the :name Blockchain.',
        'image'       => 'metadata/validators.png',
    ],

    'validator-monitor' => [
        'title'       => 'Validator Monitor | :name Blockchain Explorer',
        'description' => 'Validator Monitor allows real-time observation of block production and validators\' participation in each round.',
        'image'       => 'metadata/validator-monitor.png',
    ],

    'transactions'  => [
        'title'       => 'Transactions | :name Blockchain Explorer',
        'description' => 'View transaction details from the :name Blockchain.',
        'image'       => 'metadata/transactions.png',
    ],

    'transaction'   => [
        'title'       => 'Transaction :txid Details | :name Blockchain Explorer',
        'description' => 'View information and details for transaction with ID :txid on the :name Blockchain.',
        'image'       => 'metadata/transaction.png',
    ],

    'blocks'        => [
        'title'       => 'Blocks | :name Blockchain Explorer',
        'description' => 'View details for blocks on the :name Blockchain.',
        'image'       => 'metadata/blocks.png',
    ],

    'block'         => [
        'title'       => 'Block :blockid Details | :name Blockchain Explorer',
        'description' => 'View information and details for block with ID :blockid on the :name Blockchain.',
        'image'       => 'metadata/block.png',
    ],

    'top-accounts'  => [
        'title'       => 'Top Accounts | :name Blockchain Explorer',
        'description' => 'View wallet address details on the :name Blockchain.',
        'image'       => 'metadata/top-accounts.png',
    ],

    'exchanges'  => [
        'title'       => 'Exchanges and Aggregators | :name Blockchain Explorer',
        'description' => 'A list of active exchanges and aggregators/swaps that support ARK Token.',
        'image'       => 'metadata/exchanges.png',
    ],

    'compatible-wallets'  => [
        'title'       => 'Compatible Wallets | :name Blockchain Explorer',
        'description' => 'List of web, desktop, and mobile wallets that support ARK Token.',
        'image'       => 'metadata/compatible-wallets.png',
    ],

    'wallet'        => [
        'title'       => 'Wallet :address Details | :name Blockchain Explorer',
        'description' => 'View balance, transaction history, and other details for :address on the :name Blockchain.',
        'image'       => 'metadata/wallet.png',
    ],

    'wallet-voters' => [
        'title'       => 'Voters of :validator | :name Blockchain Explorer',
        'description' => 'View all voters of :validator on the :name Blockchain.',
        'image'       => 'metadata/wallets.png',
    ],

    'wallet-blocks' => [
        'title'       => 'Blocks Validated by :validator | :name Blockchain Explorer',
        'description' => 'View blocks validated by :validator on the :name Blockchain.',
        'image'       => 'metadata/wallets.png',
    ],

    '404'           => [
        'title'       => '404 :error | :name Blockchain Explorer',
        'description' => '404 :error',
        'image'       => 'metadata/404.png',
    ],

    '500'           => [
        'title'       => '500 Internal Server Error | :name Blockchain Explorer',
    ],
    
    'support'       => [
        'title'       => 'Support | :name Blockchain Explorer',
        'description' => 'Get help with the use of the :name Blockchain Explorer.',
        'image'       => 'metadata/support.png',
    ],
];
