<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class TransactionStatistics
{
    public function __construct(
        public array $details,
        public TransactionAveragesStatistics $averages,
        public TransactionRecordsStatistics $records,
    ) {
        //
    }

    public static function make(
        array $details,
        TransactionAveragesStatistics $averages,
        TransactionRecordsStatistics $records,
    ): self {
        return new self(
            $details,
            $averages,
            $records,
        );
    }
}
