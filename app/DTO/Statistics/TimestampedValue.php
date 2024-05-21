<?php

declare(strict_types=1);

namespace App\DTO\Statistics;

final class TimestampedValue
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

    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp,
            'value'     => $this->value,
        ];
    }
}
