// https://github.com/EugeneMeles/laravel-react-i18n/blob/master/src/plugin/helper.ts

import path from "path";

/**
 *
 * @param rawDirname
 */
export function dirnameSanitize(rawDirname: string): string {
    return rawDirname.replace(/[\\/]+/g, path.sep).replace(/[\\/]+$/, "") + path.sep;
}
