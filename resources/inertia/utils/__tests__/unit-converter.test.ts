import { formatGwei, formatUnits, gweiToArk, parseUnits, weiToArk } from "../UnitConverter";

describe("formatGwei", () => {
    it("returns integer values as-is", () => {
        expect(formatGwei("5")).toBe("5");
        expect(formatGwei(10)).toBe("10");
        expect(formatGwei("0")).toBe("0");
    });

    it("limits to 2 decimal places", () => {
        expect(formatGwei("1.234")).toBe("1.23");
        expect(formatGwei("0.999")).toBe("1");
        expect(formatGwei("5.678")).toBe("5.68");
    });

    it("strips trailing zeros", () => {
        expect(formatGwei("1.10")).toBe("1.1");
        expect(formatGwei("1.00")).toBe("1");
        expect(formatGwei("2.50")).toBe("2.5");
    });

    it("handles values with fewer than 2 decimals", () => {
        expect(formatGwei("1.5")).toBe("1.5");
        expect(formatGwei("3.1")).toBe("3.1");
    });
});

describe("parseUnits", () => {
    it("parses wei values", () => {
        expect(parseUnits(1, "wei").toString()).toBe("1");
    });

    it("parses gwei values", () => {
        expect(parseUnits(1, "gwei").toString()).toBe("1000000000");
    });

    it("parses ark values", () => {
        expect(parseUnits(1, "ark").toString()).toBe("1000000000000000000");
    });

    it("defaults to ark", () => {
        expect(parseUnits(1).toString()).toBe("1000000000000000000");
    });

    it("throws for unsupported units", () => {
        expect(() => parseUnits(1, "eth" as any)).toThrow("Unsupported unit");
    });
});

describe("formatUnits", () => {
    it("formats wei values", () => {
        expect(formatUnits(1, "wei")).toBe("1");
    });

    it("formats gwei values", () => {
        expect(formatUnits("1000000000", "gwei")).toBe("1");
    });

    it("formats ark values", () => {
        expect(formatUnits("1000000000000000000", "ark")).toBe("1");
    });

    it("formats with decimals", () => {
        expect(formatUnits("1500000000000000000", "ark", 2)).toBe("1.50");
    });

    it("defaults to ark", () => {
        expect(formatUnits("1000000000000000000")).toBe("1");
    });

    it("throws for unsupported units", () => {
        expect(() => formatUnits(1, "eth" as any)).toThrow("Unsupported unit");
    });
});

describe("weiToArk", () => {
    it("converts wei to ark", () => {
        expect(weiToArk("1000000000000000000")).toBe("1");
    });

    it("appends suffix when provided", () => {
        expect(weiToArk("1000000000000000000", "ARK")).toBe("1 ARK");
    });

    it("applies decimals", () => {
        expect(weiToArk("1500000000000000000", undefined, 2)).toBe("1.50");
    });
});

describe("gweiToArk", () => {
    it("converts gwei to ark", () => {
        expect(gweiToArk("1000000000")).toBe("1");
    });

    it("appends suffix when provided", () => {
        expect(gweiToArk("1000000000", "ARK")).toBe("1 ARK");
    });

    it("strips trailing zeros", () => {
        expect(gweiToArk("1500000000")).toBe("1.5");
    });
});
