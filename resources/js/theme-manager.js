const ThemeManager = () => {
    return Alpine.reactive({
        _theme: localStorage.theme,

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

            document.dispatchEvent(
                new CustomEvent("setThemeMode", {
                    detail: {
                        theme: value,
                    },
                })
            );
        },
    });
};

export default ThemeManager;
