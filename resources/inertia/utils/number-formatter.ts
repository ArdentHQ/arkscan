import useConfig from "@/hooks/use-config";

export const CRYPTO_DECIMALS = 8;

export const CRYPTO_DECIMALS_SMALL = 8;

export const FIAT_DECIMALS = 2;

export const FIAT_DECIMALS_SMALL = 4;

export function hasSymbol(currency: string): boolean {
    const { currencies } = useConfig();

    return currencies![currency]?.symbol !== null;
}

export function isFiat(currency: string): boolean {
    const { currencies } = useConfig();

    if (currencies![currency] === undefined) {
        return false;
    }

    return currencies![currency]?.locale !== null;
}

export function currency(value: number, currency: string, showSmallAmounts = false): string {
    const isSmallAmount = Math.abs(value) < 1;
    const decimals = decimalsFor(currency, showSmallAmounts && isSmallAmount);

    if (!isFiat(currency)) {
        const { currencies } = useConfig();
        const symbol = currencies![currency]?.symbol ?? currency;
        const formatted = new Intl.NumberFormat("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(value);

        return `${symbol} ${formatted}`;
    }

    const { currencies } = useConfig();
    const locale = currencies![currency]?.locale ?? "en-US";

    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);
}

export function currencyWithDecimals(value: number, currency: string, decimals?: number, hideCurrency = false): string {
    if (isFiat(currency)) {
        const { currencies } = useConfig();
        const locale = currencies![currency]?.locale ?? "en-US";
        const maximumFractionDigits = decimals ?? 4;

        // Workaround similar to the PHP version: round the numeric value to the requested
        // number of decimals before formatting to avoid unexpected rounding behaviour.
        const rounded = Number(Number(value).toFixed(maximumFractionDigits));

        if (hideCurrency) {
            return new Intl.NumberFormat("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits,
            }).format(rounded);
        }

        return new Intl.NumberFormat(locale, {
            style: "currency",
            currency,
            minimumFractionDigits: 2,
            maximumFractionDigits,
        }).format(rounded);
    }

    // Non-fiat (crypto) use the provided decimals or fall back to CRYPTO_DECIMALS.
    const { currencies } = useConfig();
    const symbol = currencies![currency]?.symbol ?? currency;
    const usedDecimals = decimals ?? CRYPTO_DECIMALS;

    const formatted = new Intl.NumberFormat("en-US", {
        minimumFractionDigits: usedDecimals,
        maximumFractionDigits: usedDecimals,
    }).format(value);

    return hideCurrency ? formatted : `${formatted} ${symbol}`;
}

export function networkCurrency(value: number | string, decimals = 8, withSuffix = false): string {
    const numeric = Number(value) || 0;

    // Workaround similar to PHP/other formatters:
    // round the numeric value to the requested number of decimals before formatting
    // to avoid unexpected rounding behaviour.
    const rounded = Number(numeric.toFixed(decimals));

    const formatted = new Intl.NumberFormat("en-US", {
        minimumFractionDigits: Math.min(2, decimals),
        maximumFractionDigits: decimals,
    }).format(rounded);

    if (!withSuffix) {
        return formatted;
    }

    // Try common config keys for the network currency, fall back to a sensible default.
    const cfg = useConfig();
    const networkCurrency =
        // common possible shapes:
        // { network: { currency: 'ARK' } }
        cfg.network?.currency ??
        // { networkCurrency: 'ARK' }
        (cfg as any).networkCurrency ??
        // { defaults: { networkCurrency: 'ARK' } }
        cfg.defaults?.networkCurrency ??
        // last resort
        "ARK";

    return `${formatted} ${networkCurrency}`;
}

export function decimalsFor(currency: string, isSmallValue = false): number {
    if (isFiat(currency)) {
        return isSmallValue ? FIAT_DECIMALS_SMALL : FIAT_DECIMALS;
    }
    return isSmallValue ? CRYPTO_DECIMALS_SMALL : CRYPTO_DECIMALS;
}
