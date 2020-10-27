<?php

declare(strict_types=1);

namespace App\Services;

use Brick\Math\BigDecimal;
use Stringable;

final class BigNumber implements Stringable
{
    private BigDecimal $value;

    /**
     * @param int|float|string $value
     */
    private function __construct($value)
    {
        $this->value = BigDecimal::of($value);
    }

    /**
     * @param int|float|string $value
     */
    public static function new($value): self
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

    /**
     * @param BigDecimal|int|string $value
     */
    public function minus($value): self
    {
        $this->value = $this->value->minus($value);

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
        return $this->value->exactlyDividedBy(1e8)->toFloat();
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
