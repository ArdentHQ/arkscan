<?php

namespace App\DTO\Statistics;

class TimestampedValue
{
    public function __construct(public ?int $timestamp = null, public ?float $value = null)
    {
        //
    }

    public static function fromArray(?array $data): self
    {
        if ($data === null) {
            return new self();
        }

        return new self($data['timestamp'], $data['value']);
    }
}
