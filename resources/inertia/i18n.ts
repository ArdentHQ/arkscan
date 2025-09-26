import i18n from "i18next";
import { initReactI18next } from "react-i18next";

export default async function loadI18n() {
    const files = {
        'translation': import.meta.glob('../lang/*.json'),
        'ui': import.meta.glob('../../vendor/arkecosystem/foundation/resources/lang/*.json'),
    };

    const resources: Record<string, any> = {};
    for (const [key, pathFiles] of Object.entries(files)) {
        for (const path in pathFiles) {
            const match = path.match(/..\/lang\/php_(.*)\.json$/);
            if (! match) {
                continue;
            }

            if (! resources[match[1]]) {
                resources[match[1]] = {};
            }

            resources[match[1]][key] = JSON.parse(JSON.stringify((await pathFiles[path]()).default).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}"));
        }
    }

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
