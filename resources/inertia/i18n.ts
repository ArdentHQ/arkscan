import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import defaultTranslations from "../lang/php_en.json";
import uiTranslations from "../lang/ui/php_en.json";

export default async function loadI18n() {
    const resources: Record<string, any> = {
        en: {
            'translation': JSON.parse(JSON.stringify(defaultTranslations).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}")),
            'ui': JSON.parse(JSON.stringify(uiTranslations).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}")),
        },
    };

    i18n
        .use(initReactI18next) // passes i18n down to react-i18next
        .init({
            resources,
            lng: "en", // language to use, more information here: https://www.i18next.com/overview/configuration-options#languages-namespaces-resources
            // you can use the i18n.changeLanguage function to change the language manually: https://www.i18next.com/overview/api#changelanguage
            // if you're using a language detector, do not define the lng option

            interpolation: {
                escapeValue: false,
            },
        });
}
