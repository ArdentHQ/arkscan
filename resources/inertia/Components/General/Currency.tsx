export default function Currency({
    currency,
    decimals,
    minDecimals,
    value,
}: {
    currency: string;
    decimals?: number;
    minDecimals?: number;
    value: string | number;
}) {
    const numValue = typeof value === "string" ? Number(value) : value;
    const maxDecimals = decimals !== undefined ? decimals : 2;
    const minimumDecimals = minDecimals !== undefined ? minDecimals : Math.min(2, maxDecimals);
    const result = Number.isFinite(numValue)
        ? numValue.toLocaleString(undefined, {
              minimumFractionDigits: minimumDecimals,
              maximumFractionDigits: maxDecimals,
          })
        : value;

    return `${result} ${currency.toUpperCase()}`;
}
