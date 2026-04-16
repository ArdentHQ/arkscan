jest.mock("@inertiajs/react", () => ({
    router: { reload: jest.fn(), on: jest.fn() },
    usePage: jest.fn(() => ({ props: {} })),
}));

import { resolveTabQueryStringValues } from "../TabsProvider";
import { ITabsQueryString } from "../types";

describe("resolveTabQueryStringValues", () => {
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
            page: "1",
            "per-page": "25",
            outgoing: "true",
        });
    });
});
