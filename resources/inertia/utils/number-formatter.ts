import useSharedData from "@/hooks/use-shared-data";

const FIAT_DECIMALS = 2;
const FIAT_DECIMALS_SMALL = 4;
const CRYPTO_DECIMALS = 8;

export function hasSymbol(currency: string): boolean {
    const { currencies } = useSharedData();

    return currencies![currency]?.symbol !== null;
}

export function isFiat(currency: string): boolean {
    const { currencies } = useSharedData();

    if (currencies![currency.toLowerCase()] === undefined) {
        return false;
    }

    return currencies![currency.toLowerCase()]?.locale !== null;
}

export function currency(value: number, currency: string, showSmallAmounts = false): string {
    const isSmallAmount = Math.abs(value) < 1;
    const decimals = decimalsFor(currency, showSmallAmounts && isSmallAmount);

    if (!isFiat(currency)) {
        const { currencies } = useSharedData();
        const symbol = currencies![currency]?.symbol ?? currency;
        const formatted = new Intl.NumberFormat("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(value);

        return `${symbol} ${formatted}`;
    }

    const { currencies } = useSharedData();
    const locale = currencies![currency]?.locale ?? "en-US";

    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    }).format(value);
}

export function currencyWithDecimals({
    value,
    currency,
    decimals,
    showSmallAmounts = false,
    hideCurrency = false,
}: {
    value: number;
    currency: string;
    decimals?: number;
    showSmallAmounts?: boolean;
    hideCurrency?: boolean;
}): string {
    const isSmallAmount = value < 1;
    let effectiveDecimals: number;

    if (isFiat(currency)) {
        const { currencies } = useSharedData();
        const locale = currencies![currency]?.locale ?? "en-US";

        // Dynamically choose decimals for fiat: FIAT_DECIMALS_SMALL for small amounts if showSmallAmounts, else FIAT_DECIMALS; override if decimals provided
        effectiveDecimals = decimals ?? (showSmallAmounts && isSmallAmount ? FIAT_DECIMALS_SMALL : FIAT_DECIMALS);

        // Round the value to avoid unexpected rounding in Intl.NumberFormat
        const rounded = Number(Number(value).toFixed(effectiveDecimals));

        if (hideCurrency) {
            const formatted = new Intl.NumberFormat("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: effectiveDecimals,
            }).format(rounded);
            return formatted;
        }

        const formatted = new Intl.NumberFormat(locale, {
            style: "currency",
            currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: effectiveDecimals,
        }).format(rounded);
        return formatted;
    }

    // Non-fiat (crypto) use the provided decimals or fall back to CRYPTO_DECIMALS.
    const { currencies } = useSharedData();
    const symbol = currencies![currency]?.symbol ?? currency;
    effectiveDecimals = decimals ?? CRYPTO_DECIMALS;

    let formatted = new Intl.NumberFormat("en-US", {
        minimumFractionDigits: effectiveDecimals,
        maximumFractionDigits: effectiveDecimals,
    }).format(value);
    // Strip trailing zeros and decimal point if no fractional part remains (for crypto only)
    formatted = stripTrailingZeros(formatted);

    return hideCurrency ? formatted : `${formatted} ${symbol}`;
}

// Helper function to strip trailing zeros after formatting (used for crypto)
function stripTrailingZeros(str: string): string {
    // Split into integer and fractional parts
    const parts = str.split(".");
    if (parts.length < 2) return str; // No decimal, return as is

    // Remove trailing zeros from fractional part
    let fractional = parts[1].replace(/0+$/, "");
    // If fractional is empty, remove the decimal point too
    if (fractional === "") {
        return parts[0];
    }
    return `${parts[0]}.${fractional}`;
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
    const cfg = useSharedData();
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
