<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

use Illuminate\Support\Collection;
use Livewire\Wireable;

final class AddressHoldingStatistics implements Wireable
{
    public int $greaterThanOne;

    public int $greaterThanOneThousand;

    public int $greaterThanTenThousand;

    public int $greaterThanOneHundredThousand;

    public int $greaterThanOneMillion;

    public function __construct(array $data)
    {
        $grouped = (new Collection($data))->pluck('count', 'grouped');

        $this->greaterThanOne                = $grouped->get(1, 0);
        $this->greaterThanOneThousand        = $grouped->get(1000, 0);
        $this->greaterThanTenThousand        = $grouped->get(10000, 0);
        $this->greaterThanOneHundredThousand = $grouped->get(100000, 0);
        $this->greaterThanOneMillion         = $grouped->get(1000000, 0);
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            1       => $this->greaterThanOne,
            1000    => $this->greaterThanOneThousand,
            10000   => $this->greaterThanTenThousand,
            100000  => $this->greaterThanOneHundredThousand,
            1000000 => $this->greaterThanOneMillion,
        ];
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public static function fromLivewire($value)
    {
        $self = new self([]);

        $self->greaterThanOne                = $value[1];
        $self->greaterThanOneThousand        = $value[1000];
        $self->greaterThanTenThousand        = $value[10000];
        $self->greaterThanOneHundredThousand = $value[100000];
        $self->greaterThanOneMillion         = $value[1000000];

        return $self;
    }
}
