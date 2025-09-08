import i18n from "i18next";
import { initReactI18next } from "react-i18next";

const files = import.meta.glob('../lang/*.json');
const resources: Record<string, any> = {};
for (const path in files) {
    const match = path.match(/..\/lang\/php_(.*)\.json$/);
    if (! match) {
        continue;
    }

    resources[match[1]] = {
        translation: JSON.parse(JSON.stringify((await files[path]()).default).replace(/:([A-Za-z0-9_]+)/g, "{{$1}}"))
    };
}

console.log('i18n resources', resources);

i18n
    .use(initReactI18next) // passes i18n down to react-i18next
    .init({
        resources,
        lng: "en", // language to use, more information here: https://www.i18next.com/overview/configuration-options#languages-namespaces-resources
        // you can use the i18n.changeLanguage function to change the language manually: https://www.i18next.com/overview/api#changelanguage
        // if you're using a language detector, do not define the lng option

        interpolation: {
            escapeValue: false,
            // prefix: ":",
            // suffix: "",
        },
    });

export default i18n;
