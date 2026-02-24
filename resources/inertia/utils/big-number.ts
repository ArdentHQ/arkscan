import BigNumber from "bignumber.js";

export default BigNumber;

export function toFloat(value: BigNumber.Value, divisor: number = 1e18, scale?: number): number {
    let result = new BigNumber(value).dividedBy(divisor);
    if (scale !== undefined) {
        result = result.precision(scale, BigNumber.ROUND_DOWN);
    }

    return result.toNumber();
}
