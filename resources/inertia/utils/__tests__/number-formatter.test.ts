jest.mock("@/hooks/use-shared-data", () => ({
    __esModule: true,
    default: () => ({
        network: { currency: "ARK" },
    }),
}));

import { formatWithCurrencyCustom, networkCurrency } from "../number-formatter";

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

    it("does not round up a value slightly below 1 to 1", () => {
        expect(networkCurrency("0.9999999999999885", 8)).toBe("0.99999999");
    });
});

describe("formatWithCurrencyCustom", () => {
    it("does not round up a value slightly below 1 to 1", () => {
        expect(formatWithCurrencyCustom("0.9999999999999885", "ARK", null)).toBe("0.99999999 ARK");
    });

    it("does not round up a value slightly below a whole number (2 decimals)", () => {
        expect(formatWithCurrencyCustom("0.999999", "ARK", 2)).toBe("0.99 ARK");
    });
});
