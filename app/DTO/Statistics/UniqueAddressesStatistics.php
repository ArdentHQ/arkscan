<?php

namespace App\DTO\Statistics;

class UniqueAddressesStatistics
{
    public static function make(
        $genesis,
        $newest,
        $mostTransactions,
        $largest,
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
        public array $genesis,
        public array $newest,
        public array $mostTransactions,
        public array $largest,
    ) {
        //
    }
}
