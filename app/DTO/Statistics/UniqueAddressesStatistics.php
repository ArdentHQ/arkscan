<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class UniqueAddressesStatistics
{
    public static function make(
        ?array $genesis,
        ?array $newest,
        ?array $mostTransactions,
        ?array $largest,
    ): self
    {
        return new self(
            $genesis,
            $newest,
            $mostTransactions,
            $largest,
        );
    }

    public function __construct(
        public ?array $genesis,
        public ?array $newest,
        public ?array $mostTransactions,
        public ?array $largest,
    ) {
        //
    }
}
