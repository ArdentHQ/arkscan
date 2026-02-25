jest.mock("@/hooks/use-shared-data", () => ({
    __esModule: true,
    default: () => ({
        network: { currency: "ARK" },
    }),
}));

import { networkCurrency } from "../number-formatter";

describe("networkCurrency", () => {
    it("formats a very large value without losing integer precision", () => {
        expect(networkCurrency("123456789123456789123456789.9876543219876543", 8)).toBe(
            "123,456,789,123,456,789,123,456,789.98765432",
        );
    });

    it("keeps grouped thousands for large integers", () => {
        expect(networkCurrency("1000000000000000000000000", 8)).toBe("1,000,000,000,000,000,000,000,000.00");
    });

    it("keeps stable trailing-zero behaviour with at least two decimals", () => {
        expect(networkCurrency("1000.5", 8)).toBe("1,000.50");
        expect(networkCurrency("1000.000000001", 8)).toBe("1,000.00");
    });

    it("formats negative large values without rounding the integer part", () => {
        expect(networkCurrency("-123456789123456789123456789.9876543219876543", 8)).toBe(
            "-123,456,789,123,456,789,123,456,789.98765432",
        );
    });
});
