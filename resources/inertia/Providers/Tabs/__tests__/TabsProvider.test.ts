import { arePathAndSearchEqual, resolveTabQueryStringValues } from "../TabsProvider";
import { ITabsQueryString } from "../types";

describe("TabsProvider helpers", () => {
    it("hydrates tab query string values from the current URL", () => {
        const defaults: ITabsQueryString = {
            transactions: {
                page: 1,
                "per-page": 25,
                outgoing: true,
            },
        };

        const url = new URL("https://arkscan.test/addresses/abc?page=6&per-page=50&outgoing=false");

        expect(resolveTabQueryStringValues(defaults, "transactions", url)).toEqual({
            page: "6",
            "per-page": "50",
            outgoing: "false",
        });
    });

    it("falls back to defaults when query params are missing", () => {
        const defaults: ITabsQueryString = {
            transactions: {
                page: 1,
                "per-page": 25,
                outgoing: true,
            },
        };

        const url = new URL("https://arkscan.test/addresses/abc");

        expect(resolveTabQueryStringValues(defaults, "transactions", url)).toEqual({
            page: 1,
            "per-page": 25,
            outgoing: true,
        });
    });

    it("compares only path and query string when deciding if a URL changed", () => {
        const left = new URL("https://arkscan.test/addresses/abc?page=6");
        const right = new URL("https://different-host.test/addresses/abc?page=6");

        expect(arePathAndSearchEqual(left, right)).toBe(true);
    });
});
