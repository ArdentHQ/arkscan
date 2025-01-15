<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CryptoCurrencies;
use App\Facades\Network;
use ARKEcosystem\Foundation\NumberFormatter\NumberFormatter as BetterNumberFormatter;
use ARKEcosystem\Foundation\NumberFormatter\ResolveScientificNotation;
use ReflectionClass;

final class NumberFormatter
{
    public const CRYPTO_DECIMALS = 8;

    public const CRYPTO_DECIMALS_SMALL = 8;

    public const FIAT_DECIMALS = 2;

    public const FIAT_DECIMALS_SMALL = 4;

    /**
     * @param string|int|float $value
     */
    public static function number($value): string
    {
        return BetterNumberFormatter::new()->formatWithDecimal((float) $value);
    }

    /**
     * @param string|int|float $value
     */
    public static function percentage($value): string
    {
        return BetterNumberFormatter::new()->formatWithPercent((float) $value, 2);
    }

    /**
     * @param string|int|float $value
     */
    public static function satoshi($value): string
    {
        return BetterNumberFormatter::new()->formatWithDecimal(BigNumber::new($value)->toFloat());
    }

    /**
     * @param string|int|float $value
     */
    public static function currency($value, string $currency, bool $showSmallAmounts = false): string
    {
        $isSmallAmount = $value < 1;

        if (! static::isFiat($currency)) {
            return BetterNumberFormatter::new()
                ->formatWithCurrencyCustom($value, $currency, static::decimalsFor($currency, $showSmallAmounts && $isSmallAmount));
        }

        return BetterNumberFormatter::new()
            ->withLocale('en-US')
            ->withFractionDigits(static::decimalsFor($currency, $showSmallAmounts && $isSmallAmount))
            ->formatCurrency((float) $value, $currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyWithDecimals(
        $value,
        string $currency,
        ?int $decimals = 4,
        ?int $maxIntegerDigitsForDecimals = null
    ): string {
        $floatValue      = (float) str_replace(',', '.', (string) $value);
        $intLen          = strlen((string) (int) abs($floatValue));
        
        if ($maxIntegerDigitsForDecimals !== null && $intLen >= $maxIntegerDigitsForDecimals) {
            $decimals = 0;
        }

        if ($decimals === null) {
            // Default decimals if not provided
            $decimals = self::isFiat($currency) ? 4 : 8;
        }

        // Workaround for rounding (e.g., 1.00005 => 1.0001)
        $roundedValue = (float) number_format($floatValue, $decimals, '.', '');

        $formatter = BetterNumberFormatter::new()
            ->withLocale('en-US')
            ->withFractionDigits($decimals)
            ->withMinFractionDigits($decimals > 0 ? 2 : 0);

        if (self::isFiat($currency)) {
            return $formatter->formatCurrency($roundedValue, $currency);
        }

        return $formatter->formatWithCurrencyCustom($roundedValue, $currency, $decimals);
    }

    /**
     * @param string|int|float $value
     */
    public static function networkCurrency($value, int $decimals = 8, bool $withSuffix = false): string
    {
        $value = BetterNumberFormatter::new()
            ->withLocale('en-US')
            ->withFractionDigits($decimals)
            ->withMinFractionDigits(min(2, $decimals))
            // Workaround to fix 5 rounding down (e.g. 1.00005 > 1 instead of 1.0001)
            ->formatWithDecimal(floatval(number_format((float) $value, $decimals, '.', '')));

        if ($withSuffix) {
            return $value.' '.Network::currency();
        }

        return $value;
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyWithoutSuffix($value, string $currency): string
    {
        return trim(BetterNumberFormatter::new()->formatWithCurrencyCustom($value, '', static::decimalsFor($currency)));
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyWithDecimalsWithoutSuffix($value, string $currency): string
    {
        return number_format((float) ResolveScientificNotation::execute((float) $value), static::decimalsFor($currency));
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyShort($value, string $currency): string
    {
        return BetterNumberFormatter::new()->formatWithCurrencyShort($value, $currency);
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyShortNotation($value): string
    {
        $value = is_string($value) ? (float) $value : $value;

        if ($value < 1000) {
            return sprintf('%d', $value);
        }

        if ($value < 1000000) {
            return sprintf('%d%s', number_format($value / 1000, 3), 'K');
        }

        return sprintf('%0.2f%s', number_format($value / 1000000, 6), 'M');
    }

    /**
     * @param string|int|float $value
     */
    public static function currencyForViews($value, string $currency): string
    {
        if (self::isFiat($currency)) {
            return trim(trim(self::currencyWithDecimals($value, $currency, 0), '0'), '.');
        }

        return BetterNumberFormatter::new()
            ->formatWithCurrencyCustom(
                $value,
                $currency,
                self::CRYPTO_DECIMALS
            );
    }

    public static function isFiat(string $currency): bool
    {
        $cryptoCurrencies = (new ReflectionClass(CryptoCurrencies::class))->getConstants();

        return ! in_array($currency, $cryptoCurrencies, true);
    }

    public static function decimalsFor(string $currency, bool $isSmallValue = false): int
    {
        if (static::isFiat($currency)) {
            return $isSmallValue ? self::FIAT_DECIMALS_SMALL : self::FIAT_DECIMALS;
        }

        return $isSmallValue ? self::CRYPTO_DECIMALS_SMALL : self::CRYPTO_DECIMALS;
    }

    public static function hasSymbol(string $currency): bool
    {
        if (! self::isFiat($currency)) {
            return false;
        }

        return config('currencies.'.strtolower($currency).'.symbol') !== null;
    }
}
