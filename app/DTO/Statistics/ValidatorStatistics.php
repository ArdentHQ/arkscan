<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

final class ValidatorStatistics
{
    public ?WalletViewModel $mostUniqueVoters = null;

    public ?WalletViewModel $leastUniqueVoters = null;

    public ?WalletViewModel $mostBlocksForged = null;

    public function __construct(
        ?Wallet $mostUniqueVoters = null,
        ?Wallet $leastUniqueVoters = null,
        ?Wallet $mostBlocksForged = null,
        public ?WalletWithValue $oldestActiveValidator = null,
        public ?WalletWithValue $newestActiveValidator = null,
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
        ?WalletWithValue $oldestActiveValidator = null,
        ?WalletWithValue $newestActiveValidator = null,
    ): self {
        return new self(
            $mostUniqueVoters,
            $leastUniqueVoters,
            $mostBlocksForged,
            $oldestActiveValidator,
            $newestActiveValidator,
        );
    }
}
