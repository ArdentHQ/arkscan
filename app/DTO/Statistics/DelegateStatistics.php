<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

final class DelegateStatistics
{
    public ?WalletViewModel $mostUniqueVoters = null;

    public ?WalletViewModel $leastUniqueVoters = null;

    public ?WalletViewModel $mostBlocksForged = null;

    public function __construct(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        public ?WalletWithValue $oldestActiveDelegate = null,
        public ?WalletWithValue $newestActiveDelegate = null,
    ) {
        if ($mostUniqueVoters !== null) {
            $this->mostUniqueVoters = new WalletViewModel($mostUniqueVoters);
        }

        if ($leastUniqueVoters !== null) {
            $this->leastUniqueVoters = new WalletViewModel($leastUniqueVoters);
        }

        if ($mostBlocksForged !== null) {
            $this->mostBlocksForged = new WalletViewModel($mostBlocksForged);
        }
    }

    public static function make(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        ?WalletWithValue $oldestActiveDelegate = null,
        ?WalletWithValue $newestActiveDelegate = null,
    ): self {
        return new self(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveDelegate,
            $newestActiveDelegate,
        );
    }
}
