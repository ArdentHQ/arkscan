<?php

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

class DelegateStatistics
{
    public ?WalletViewModel $mostUniqueVoters = null;
    public ?WalletViewModel $leastUniqueVoters = null;
    public ?WalletViewModel $mostBlocksForged = null;

    public static function make(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        ?WalletWithValue $oldestActiveDelegate = null,
        ?WalletWithValue $newestActiveDelegate = null,
    ): self
    {
        return new self(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveDelegate,
            $newestActiveDelegate,
        );
    }

    public function __construct(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        public ?WalletWithValue $oldestActiveDelegate = null,
        public ?WalletWithValue $newestActiveDelegate = null,
    ) {
        $this->mostUniqueVoters = new WalletViewModel($mostUniqueVoters);
        $this->leastUniqueVoters = new WalletViewModel($leastUniqueVoters);
        $this->mostBlocksForged = new WalletViewModel($mostBlocksForged);
    }
}
