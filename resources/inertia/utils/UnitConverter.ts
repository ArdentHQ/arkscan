import BigNumber from "bignumber.js";

const WEI_MULTIPLIER = new BigNumber(1);
const GWEI_MULTIPLIER = new BigNumber(1_000_000_000); // 1e9
const ARK_MULTIPLIER = new BigNumber(1_000_000_000_000_000_000); // 1e18

export function parseUnits(value: number | string | BigNumber, unit: "wei" | "gwei" | "ark" = "ark"): BigNumber {
    const val = new BigNumber(value);
    switch (unit.toLowerCase()) {
        case "wei":
            return val.multipliedBy(WEI_MULTIPLIER);
        case "gwei":
            return val.multipliedBy(GWEI_MULTIPLIER);
        case "ark":
            return val.multipliedBy(ARK_MULTIPLIER);
        default:
            throw new Error(`Unsupported unit: ${unit}. Supported units are 'wei', 'gwei', and 'ark'.`);
    }
}

export function formatUnits(
    value: string | number | BigNumber,
    unit: "wei" | "gwei" | "ark" = "ark",
    decimals?: number,
): string {
    const val = new BigNumber(value);
    let divisor: BigNumber;
    switch (unit.toLowerCase()) {
        case "wei":
            divisor = WEI_MULTIPLIER;
            break;
        case "gwei":
            divisor = GWEI_MULTIPLIER;
            break;
        case "ark":
            divisor = ARK_MULTIPLIER;
            break;
        default:
            throw new Error(`Unsupported unit: ${unit}. Supported units are 'wei', 'gwei', and 'ark'.`);
    }

    if (decimals !== undefined) {
        return val.dividedBy(divisor).toFixed(decimals);
    }

    return val.dividedBy(divisor).toString();
}

export function weiToArk(value: string | number | BigNumber, suffix?: string, decimals?: number): string {
    const arkValue = formatUnits(parseUnits(value, "wei"), "ark", decimals);
    const result = arkValue.toString();

    return suffix ? `${result} ${suffix}` : result;
}

export function gweiToArk(value: string | number | BigNumber, suffix?: string): string {
    const arkValue = formatUnits(parseUnits(value, "gwei"), "ark");
    const result = arkValue.toString().replace(/\.?0+$/, ""); // strip trailing zeros
    return suffix ? `${result} ${suffix}` : result;
}
