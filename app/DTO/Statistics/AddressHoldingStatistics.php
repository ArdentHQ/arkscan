<?php

namespace App\DTO\Statistics;

use Illuminate\Support\Collection;

class AddressHoldingStatistics
{
    public Collection $data;

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->data = (new Collection($data))
            ->pluck('count', 'grouped');
    }
}
