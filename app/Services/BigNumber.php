<?php

declare(strict_types=1);

namespace App\Services;

use Brick\Math\BigDecimal;
use Stringable;

final class BigNumber implements Stringable
{
    private BigDecimal $value;

    private function __construct(int|float|string $value)
    {
        $this->value = BigDecimal::of(is_float($value) ? (string) $value : $value);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    public static function new(int|float|string $value): self
    {
        return new static($value);
    }

    /**
     * @param BigDecimal|int|string $value
     */
    public function plus($value): self
    {
        $this->value = $this->value->plus($value);

        return $this;
    }

    public function toNumber(): int
    {
        return $this->value->toInt();
    }

    public function toInt(): int
    {
        return intval($this->toFloat());
    }

    public function toFloat(): float
    {
        return $this->value->dividedByExact(100_000_000)->toFloat();
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }
}
