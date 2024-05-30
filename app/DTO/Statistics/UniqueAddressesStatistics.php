<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use Livewire\Wireable;

final class UniqueAddressesStatistics implements Wireable
{
    public function __construct(
        public ?array $genesis,
        public ?array $newest,
        public ?array $mostTransactions,
        public ?array $largest,
    ) {
        //
    }

    public static function make(
        ?array $genesis,
        ?array $newest,
        ?array $mostTransactions,
        ?array $largest,
    ): self {
        return new self(
            $genesis,
            $newest,
            $mostTransactions,
            $largest,
        );
    }

    public function toLivewire(): array
    {
        return [
            'genesis'          => $this->genesis,
            'newest'           => $this->newest,
            'mostTransactions' => $this->mostTransactions,
            'largest'          => $this->largest,
        ];
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function fromLivewire($value)
    {
        return new self(
            $value['genesis'],
            $value['newest'],
            $value['mostTransactions'],
            $value['largest'],
        );
    }
}
