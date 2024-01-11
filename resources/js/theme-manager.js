import * as dayjs from "dayjs";

const SPAM_THRESHOLD = 300; // Milliseconds

const ThemeManager = () => {
    return Alpine.reactive({
        _theme: localStorage.theme,
        _lastEvent: null,

        get theme() {
            return this._theme;
        },

        set theme(value) {
            this.setTheme(value);
        },

        setTheme(value) {
            if (this.theme === value) {
                return;
            }

            this._theme = value;

            if (
                dayjs()
                    .subtract(SPAM_THRESHOLD, "milliseconds")
                    .isBefore(this._lastEvent)
            ) {
                return;
            }

            document.dispatchEvent(
                new CustomEvent("setThemeMode", {
                    detail: {
                        theme: value,
                    },
                })
            );

            this._lastEvent = dayjs();
        },
    });
};

export default ThemeManager;
