// Based on https://github.com/EugeneMeles/laravel-react-i18n/blob/master/src/vite.ts

import fs from "fs";

import { createLogger } from "vite";

import { convertToKeyType, saveKeyTypeToFile } from "./includes/key-type";
import parser from "./includes/parser";
import locale from "./includes/locale";

type LangPath = string | { src: string; dest: string };

interface ConfigInterface {
    paths: LangPath[];
    typeDestinationPath?: string;
    typeTranslationKeys?: boolean;
}

/**
 *
 */
export default function i18n(config: ConfigInterface) {
    const langPaths = config?.paths ? config.paths : ["lang"];

    const logger = createLogger("info", { prefix: "[laravel-react-i18n]" });

    let isPhpLocale = false;
    let files: { path: string; basename: string }[] = [];
    let exitHandlersBound = false;
    let isBuildCommand = false;
    let jsonLocales: string[] = [];
    let phpLocales: string[] = [];

    function trackFiles(nextFiles: { path: string; basename: string }[]) {
        const uniqueFiles = new Map<string, { path: string; basename: string }>();

        [...files, ...nextFiles].forEach((file) => uniqueFiles.set(file.path, file));

        files = [...uniqueFiles.values()];
    }

    function refreshPhpTranslations(langDirname: string, langDestDirname: string) {
        phpLocales = locale.getPhpLocale(langDirname);

        if (phpLocales.length > 0) {
            trackFiles(parser(langDirname, langDestDirname));
            isPhpLocale = true;
            return;
        }

        isPhpLocale = false;
    }

    function clean() {
        files.forEach((file) => fs.existsSync(file.path) && fs.unlinkSync(file.path));
        files = [];
    }

    function pushKeys(keys: string[], locales: string[], langDirname: string) {
        if (
            typeof process.env.VITE_LARAVEL_REACT_I18N_LOCALE !== "undefined" &&
            locales.includes(process.env.VITE_LARAVEL_REACT_I18N_LOCALE)
        ) {
            const fileName = isPhpLocale
                ? `php_${process.env.VITE_LARAVEL_REACT_I18N_LOCALE}`
                : process.env.VITE_LARAVEL_REACT_I18N_LOCALE;

            keys.push(convertToKeyType(langDirname, fileName));
        }

        if (
            typeof process.env.VITE_LARAVEL_REACT_I18N_FALLBACK_LOCALE !== "undefined" &&
            locales.includes(process.env.VITE_LARAVEL_REACT_I18N_FALLBACK_LOCALE) &&
            process.env.VITE_LARAVEL_REACT_I18N_LOCALE !== process.env.VITE_LARAVEL_REACT_I18N_FALLBACK_LOCALE
        ) {
            const fileName = isPhpLocale
                ? `php_${process.env.VITE_LARAVEL_REACT_I18N_FALLBACK_LOCALE}`
                : process.env.VITE_LARAVEL_REACT_I18N_FALLBACK_LOCALE;

            keys.push(convertToKeyType(langDirname, fileName));
        }
    }

    return {
        name: "i18n",
        enforce: "post",
        configResolved(resolvedConfig) {
            isBuildCommand = resolvedConfig.command === "build";
        },
        config() {
            const keys: string[] = [];

            for (const langPath of langPaths) {
                const langDirname = typeof langPath === "string" ? langPath : langPath.src;
                const langDestDirname = typeof langPath === "string" ? langPath : langPath.dest;

                // Check language directory is exists.
                if (!fs.existsSync(langDirname)) {
                    const msg = [
                        "Language directory is not exist, maybe you did not publish the language files with `php artisan lang:publish`.",
                        "For more information please visit: https://laravel.com/docs/10.x/localization#publishing-the-language-files",
                    ];

                    msg.map((str) => logger.error(str, { timestamp: true }));

                    return;
                }

                // JSON-file locales.
                jsonLocales = locale.getJsonLocale(langDirname);

                if (config?.typeTranslationKeys) {
                    pushKeys(keys, jsonLocales, langDirname);
                }

                // PHP-file locales.
                refreshPhpTranslations(langDirname, langDestDirname);

                if (isPhpLocale) {
                    if (config?.typeTranslationKeys) {
                        pushKeys(keys, phpLocales, langDirname);
                    }
                } else {
                    const msg = [
                        "Language directory not contain php translations files.",
                        "For more information please visit: https://laravel.com/docs/10.x/localization#introduction",
                    ];

                    msg.map((str) => logger.info(str, { timestamp: true }));
                }

                if (config?.typeTranslationKeys) {
                    saveKeyTypeToFile(keys.join("|"), config?.typeDestinationPath);
                }
            }
        },
        buildEnd() {
            if (isBuildCommand) {
                clean();
            }
        },
        handleHotUpdate(ctx: any) {
            const keys: string[] = [];

            for (const langPath of langPaths) {
                const langDirname = typeof langPath === "string" ? langPath : langPath.src;
                const langDestDirname = typeof langPath === "string" ? langPath : langPath.dest;
                const hasMissingGeneratedFile = files.some((file) => !fs.existsSync(file.path));
                const touchedLangFile = /lang\/.*\.(php|json)$/.test(ctx.file);

                if (hasMissingGeneratedFile || touchedLangFile) {
                    refreshPhpTranslations(langDirname, langDestDirname);
                }

                if (config?.typeTranslationKeys) {
                    pushKeys(keys, jsonLocales, langDirname);
                }

                if (isPhpLocale) {
                    if (config?.typeTranslationKeys) {
                        pushKeys(keys, phpLocales, langDirname);
                    }
                }

                if (config?.typeTranslationKeys) {
                    saveKeyTypeToFile(keys.join("|"), config?.typeDestinationPath);
                }
            }
        },
        configureServer() {
            if (exitHandlersBound) return;

            process.on("exit", clean);
            process.on("SIGINT", process.exit);
            process.on("SIGTERM", process.exit);
            process.on("SIGHUP", process.exit);

            exitHandlersBound = true;
        },
    };
}
