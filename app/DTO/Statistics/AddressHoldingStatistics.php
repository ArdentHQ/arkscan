<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use Illuminate\Support\Collection;

final class AddressHoldingStatistics
{
    public Collection $data;

    public function __construct(array $data)
    {
        $this->data = (new Collection($data))
            ->pluck('count', 'grouped');
    }

    public static function make(array $data): self
    {
        return new self($data);
    }
}
