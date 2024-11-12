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
     * @codeCoverageIgnore
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @param int|float|string $value
     */
    public static function new($value): self
    {
        return new static($value);
    }

    public static function zero(): self
    {
        return new static(0);
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
     * @param BigDecimal|int|float|string $value
     */
    public function multipliedBy($value): self
    {
        $this->value = $this->value->multipliedBy($value);

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

    /**
     * @param float|null $divisor Defaults to 1e18 if not provided
     *
     * @return float
     */
    public function toFloat(?float $divisor = null): float
    {
        if ($divisor === null) {
            $divisor = config('currencies.notation.crypto', 1e18);
        }

        return $this->value->exactlyDividedBy($divisor)->toFloat();
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }
}
