<?php

namespace App\DTO\Statistics;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

class TransactionStatistics
{
    public static function make(
        array $details,
        array $averages,
        array $records,
    ): self
    {
        return new self(
            $details,
            $averages,
            $records,
        );
    }

    public function __construct(
        public array $details,
        public array $averages,
        public array $records,
    ) {
        //
    }
}
