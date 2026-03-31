import dayjs from "dayjs";
import {
    formatAge,
    formatDate,
    formatDateKey,
    formatDateTime,
    formatDayjsDateTime,
    formatLocalizedDateTime,
    formatUnixDateTime,
    parseTimestamp,
} from "../formatter";

describe("formatDate", () => {
    it("formats unix seconds to date", () => {
        const ts = dayjs("2024-03-15").unix();
        expect(formatDate(ts)).toBe("15 Mar 2024");
    });
});

describe("formatDateTime", () => {
    it("formats unix seconds to date time", () => {
        const ts = dayjs("2024-03-15 14:30:45").unix();
        expect(formatDateTime(ts)).toBe("15 Mar 2024 14:30:45");
    });
});

describe("formatDayjsDateTime", () => {
    it("formats a dayjs object to date time", () => {
        const date = dayjs("2024-03-15 14:30:45");
        expect(formatDayjsDateTime(date)).toBe("15 Mar 2024 14:30:45");
    });
});

describe("formatAge", () => {
    it("returns relative and tooltip strings", () => {
        const ts = dayjs().subtract(2, "hour").unix();
        const result = formatAge(ts);

        expect(result.relative).toBe("2 hours ago");
        expect(result.tooltip).toMatch(/\d{1,2} \w{3} \d{4} \d{2}:\d{2}:\d{2}/);
    });
});

describe("formatLocalizedDateTime", () => {
    it("formats milliseconds to localized format", () => {
        const ms = dayjs("2024-03-15 14:30:45").valueOf();
        const result = formatLocalizedDateTime(ms);

        expect(result).toBeTruthy();
        expect(typeof result).toBe("string");
    });
});

describe("formatDateKey", () => {
    it("formats milliseconds to YYYY-MM-DD", () => {
        const ms = dayjs("2024-03-15 14:30:45").valueOf();
        expect(formatDateKey(ms)).toBe("2024-03-15");
    });
});

describe("formatUnixDateTime", () => {
    it("formats unix seconds to date time via dayjs.unix", () => {
        const ts = dayjs("2024-03-15 14:30:45").unix();
        expect(formatUnixDateTime(ts)).toBe("15 Mar 2024 14:30:45");
    });
});

describe("parseTimestamp", () => {
    it("handles null/undefined timestamp", () => {
        expect(parseTimestamp(null).valueOf()).toBe(0);
        expect(parseTimestamp({}).valueOf()).toBe(0);
        expect(parseTimestamp({ timestamp: null }).valueOf()).toBe(0);
    });

    it("handles object with unix field", () => {
        const ts = dayjs("2024-03-15").unix();
        const result = parseTimestamp({ timestamp: { unix: ts } });
        expect(result.format("YYYY-MM-DD")).toBe("2024-03-15");
    });

    it("handles object with epoch field", () => {
        const ts = dayjs("2024-03-15").unix();
        const result = parseTimestamp({ timestamp: { epoch: ts } });
        expect(result.format("YYYY-MM-DD")).toBe("2024-03-15");
    });

    it("handles object with human field", () => {
        const result = parseTimestamp({ timestamp: { human: "2024-03-15T00:00:00Z" } });
        expect(result.isValid()).toBe(true);
    });

    it("handles numeric seconds (<=10 digits)", () => {
        const ts = dayjs("2024-03-15").unix();
        const result = parseTimestamp({ timestamp: ts });
        expect(result.format("YYYY-MM-DD")).toBe("2024-03-15");
    });

    it("handles numeric milliseconds (>10 digits)", () => {
        const ms = dayjs("2024-03-15").valueOf();
        const result = parseTimestamp({ timestamp: ms });
        expect(result.format("YYYY-MM-DD")).toBe("2024-03-15");
    });

    it("handles string timestamp", () => {
        const result = parseTimestamp({ timestamp: "2024-03-15T00:00:00Z" });
        expect(result.isValid()).toBe(true);
    });

    it("returns invalid dayjs for unparseable values", () => {
        const result = parseTimestamp({ timestamp: "not-a-date" });
        expect(result.isValid()).toBe(false);
    });
});
