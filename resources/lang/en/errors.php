<?php

declare(strict_types=1);

return [
    'transaction_not_found'    => 'Transaction ID <span class="bg-theme-warning-50 dark:text-white dark:bg-theme-secondary-900">:transactionID</span> does not exist on the blockchain.',
    'block_not_found'          => 'Block ID <span class="bg-theme-warning-50 dark:text-white dark:bg-theme-secondary-900">:blockID</span> does not exist on the blockchain.',
    'wallet_not_found'         => 'Address <span class="hidden bg-theme-warning-50 dark:text-white dark:bg-theme-secondary-900 sm:inline">:walletID</span> <span class="bg-theme-warning-50 dark:text-white dark:bg-theme-secondary-900 sm:hidden">:truncatedWalletID</span> does not exist on the blockchain yet.',
    'wallet_not_found_details' => 'Wallet history will appear as soon as there\'s at least one confirmed transaction to the address above.',
];
