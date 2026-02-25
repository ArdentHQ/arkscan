import useSharedData from "@/hooks/use-shared-data";
import BigNumber from "bignumber.js";

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
        return formatWithCurrencyCustom(value, currency, decimals);
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

export function formatWithCurrencyCustom(
    value: number | string,
    currency: string,
    decimals: number | null = null,
): string {
    let result = Number(value).toLocaleString("en-US");

    const valueStr = String(value);

    if (valueStr.includes(".")) {
        const numericValue = Number(value);
        const effectiveDecimals = decimals ?? 8;
        result = numericValue.toFixed(effectiveDecimals);

        result = new Intl.NumberFormat("en-US", {
            minimumFractionDigits: 0,
            maximumFractionDigits: effectiveDecimals,
            useGrouping: true,
        }).format(numericValue);
    } else if (valueStr.includes(",")) {
        result = valueStr;
    }

    // Gets rid of trailing .00 if amount of decimals is 0
    if (decimals === 0 && result.includes(".")) {
        result = result.replace(/0+$/, "").replace(/\.$/, "");
    }

    return `${result} ${currency.toUpperCase()}`.trim();
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

// Helper function to strip trailing zeros after formatting. Keep minimum two decimals.
function stripTrailingZeros(str: string): string {
    // Split into integer and fractional parts
    const parts = str.split(".");
    if (parts.length < 2) return str; // No decimal, return as is

    // Remove trailing zeros from fractional part
    let fractional = parts[1].replace(/0+$/, "");
    // Ensure at least two decimals remain
    while (fractional.length < 2) {
        fractional += "0";
    }
    return `${parts[0]}.${fractional}`;
}

export function networkCurrency(
    value: number | string,
    decimals = 8,
    withCurrency = false,
    currency: string | undefined = undefined,
    suffix: string | undefined = undefined,
): string {
    const parsedValue = new BigNumber(value);
    const safeValue = parsedValue.isFinite() ? parsedValue : new BigNumber(0);
    const rounded = safeValue.decimalPlaces(decimals, BigNumber.ROUND_HALF_UP);
    const minimumFractionDigits = Math.min(2, decimals);

    let [integerPart, fractionalPart = ""] = rounded.toFixed(decimals).split(".");
    const isNegative = integerPart.startsWith("-");
    const absoluteIntegerPart = isNegative ? integerPart.slice(1) : integerPart;

    const groupedIntegerPart = absoluteIntegerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    integerPart = isNegative ? `-${groupedIntegerPart}` : groupedIntegerPart;

    if (decimals > 0) {
        fractionalPart = fractionalPart.replace(/0+$/, "");

        while (fractionalPart.length < minimumFractionDigits) {
            fractionalPart += "0";
        }
    }

    let formatted = fractionalPart.length > 0 ? `${integerPart}.${fractionalPart}` : integerPart;

    if (suffix) {
        formatted = `${formatted}${suffix}`;
    }

    if (!withCurrency) {
        return formatted;
    }

    // Try common config keys for the network currency, fall back to a sensible default.
    const cfg = useSharedData();
    const networkCurrency =
        // common possible shapes:
        currency ??
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
    return CRYPTO_DECIMALS;
}

/**
 * Formats a value in compact form with a suffix.
 *
 * Example: 1500 should return { value: 1.5, suffix: 'K' }
 *
 * Based on https://github.com/ArdentHQ/arkvault/blob/b8560d8f4a57fea4beab28c529a7699d245ae95f/src/app/lib/intl/numeral.ts#L86
 *
 * @param {BigNumber | number | string} value
 * @returns { value: number; suffix: string | undefined }
 */
export function formatCompact(value: BigNumber | string | number): { value: number; suffix: string | undefined } {
    const bnValue = new BigNumber(value);

    const scales: Array<{ exp: number; suffix: string }> = [
        { exp: 63, suffix: "Vg" }, // Vigintillion
        { exp: 60, suffix: "Nd" }, // Novemdecillion
        { exp: 57, suffix: "Od" }, // Octodecillion
        { exp: 54, suffix: "Sd" }, // Septendecillion
        { exp: 51, suffix: "Sxd" }, // Sexdecillion
        { exp: 48, suffix: "Qid" }, // Quindecillion
        { exp: 45, suffix: "Qad" }, // Quattuordecillion
        { exp: 42, suffix: "Td" }, // Tredecillion
        { exp: 39, suffix: "Dd" }, // Duodecillion
        { exp: 36, suffix: "Ud" }, // Undecillion
        { exp: 33, suffix: "D" }, // Decillion
        { exp: 30, suffix: "No" }, // Nonillion
        { exp: 27, suffix: "Oc" }, // Octillion
        { exp: 24, suffix: "Sp" }, // Septillion
        { exp: 21, suffix: "Sx" }, // Sextillion
        { exp: 18, suffix: "Qi" }, // Quintillion
        { exp: 15, suffix: "Qa" }, // Quadrillion
        { exp: 12, suffix: "T" }, // Trillion
        { exp: 9, suffix: "B" }, // Billion
        { exp: 6, suffix: "M" }, // Million
        { exp: 3, suffix: "K" }, // Thousand
    ];

    for (const { exp, suffix } of scales) {
        const threshold = new BigNumber(10).pow(exp);
        if (bnValue.isGreaterThanOrEqualTo(threshold)) {
            const scaled = bnValue.dividedBy(threshold);
            return { suffix, value: scaled.toNumber() };
        }
    }

    return { suffix: undefined, value: bnValue.toNumber() };
}
