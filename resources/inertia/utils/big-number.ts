import BigNumber from "bignumber.js";

export default BigNumber;

export function toFloat(value: BigNumber.Value, divisor: number = 1e18, scale?: number): number {
    let result = new BigNumber(value).dividedBy(divisor);
    if (scale !== undefined) {
        result = result.precision(scale, BigNumber.ROUND_DOWN);
    }

    return result.toNumber();
}

/**
 * Convert a wei string to a human-readable string preserving full precision.
 * Use this instead of toFloat when the result will be passed to a BigNumber-aware
 * formatter (e.g. networkCurrency) to avoid float64 precision loss.
 */
export function toArkString(value: BigNumber.Value, divisor: number = 1e18): string {
    return new BigNumber(value).dividedBy(divisor).toFixed();
}
