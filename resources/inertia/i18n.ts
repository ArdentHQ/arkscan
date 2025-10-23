import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import defaultTranslations from "../lang/php_en.json";
import uiTranslations from "../lang/ui/php_en.json";

export default async function loadI18n() {
    i18n.use(initReactI18next).init({
        resources: {
            en: {
                translation: JSON.parse(JSON.stringify(defaultTranslations).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}")),
                ui: JSON.parse(JSON.stringify(uiTranslations).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}")),
            },
        },

        lng: "en",

        interpolation: {
            escapeValue: false,
        },
    });
}
