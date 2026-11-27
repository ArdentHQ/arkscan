<?php

declare(strict_types=1);

namespace App\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Stringable;

final class BigNumber implements Stringable
{
    private BigDecimal $value;

    /**
     * @param int|float|string|BigDecimal $value
     */
    private function __construct($value)
    {
        $this->value = BigDecimal::of(self::toBrickValue($value));
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
     * @param int|float|string|BigDecimal $value
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
     * @param BigDecimal|int|float|string $value
     */
    public function plus($value): self
    {
        $this->value = $this->value->plus(self::toBrickValue($value));

        return $this;
    }

    /**
     * @param BigDecimal|int|float|string $value
     */
    public function multipliedBy($value): self
    {
        $this->value = $this->value->multipliedBy(self::toBrickValue($value));

        return $this;
    }

    /**
     * @param BigNumber|BigDecimal|int|float|string $value
     * @param int<0, max>|null                      $scale Defaults to the scale of the current value
     */
    public function dividedBy($value, ?int $scale = null, RoundingMode $roundingMode = RoundingMode::Unnecessary): self
    {
        if ($value instanceof self) {
            $value = $value->valueOf();
        }

        $this->value = $this->value->dividedBy(
            self::toBrickValue($value),
            $scale ?? $this->value->getScale(),
            $roundingMode,
        );

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

    public function toHex(): string
    {
        return $this->value->toBigInteger()->toBase(16);
    }

    /**
     * @param float|null       $divisor Defaults to 1e18 if not provided
     * @param int<0, max>|null $scale
     *
     * @return float
     */
    public function toFloat(?float $divisor = null, ?int $scale = null): float
    {
        if ($divisor === null) {
            $divisor = config('currencies.notation.crypto', 1e18);
        }

        $value = $this->value->dividedBy(self::toBrickValue($divisor), 18, RoundingMode::Down);

        if ($scale !== null) {
            $value = $value->toScale($scale, RoundingMode::Down);
        }

        return $value->toFloat();
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }

    /**
     * Brick doesn't accept floats, so they're passed as their string representation.
     *
     * @param int|float|string|BigDecimal $value
     */
    private static function toBrickValue($value): int|string|BigDecimal
    {
        if (is_float($value)) {
            return (string) $value;
        }

        return $value;
    }
}
