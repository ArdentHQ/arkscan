<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class LowHighValue
{
    public function __construct(public ?float $low = null, public ?float $high = null)
    {
        //
    }

    public static function fromArray(?array $data): self
    {
        if ($data === null) {
            return new self();
        }

        return new self($data['low'], $data['high']);
    }

    public function toArray(): array
    {
        return [
            'low'  => $this->low,
            'high' => $this->high,
        ];
    }
}
