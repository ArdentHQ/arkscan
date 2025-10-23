export default function Currency({
    currency,
    decimals,
    value,
}: {
    currency: string;
    decimals?: number;
    value: string | number;
}) {
    const numValue = typeof value === "string" ? Number(value) : value;
    const fixedDecimals = decimals !== undefined ? decimals : 2;
    const result = Number.isFinite(numValue)
        ? numValue.toLocaleString(undefined, {
              minimumFractionDigits: fixedDecimals,
              maximumFractionDigits: fixedDecimals,
          })
        : value;

    return `${result} ${currency.toUpperCase()}`;
}
