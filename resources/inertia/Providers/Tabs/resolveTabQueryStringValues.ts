import { ITabsQueryString } from "./types";

export function resolveTabQueryStringValues(
    defaults: ITabsQueryString,
    tab: string,
    url: URL,
): Record<string, string | number | boolean> {
    const tabDefaults = defaults[tab];

    return Object.fromEntries(
        Object.entries(tabDefaults).map(([param, value]) => [param, (url.searchParams.get(param) ?? value).toString()]),
    );
}
