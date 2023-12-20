const ThemeManager = () => {
    return Alpine.reactive({
        _theme: localStorage.theme,

        set theme(value) {
            this.setTheme(value);
        },

        get theme() {
            return this._theme;
        },

        setTheme(value) {
            document.dispatchEvent(
                new CustomEvent("setThemeMode", {
                    detail: {
                        theme: value,
                    },
                })
            );

            this.$nextTick(() => {
                this._theme = value;
            });
        },
    });
};

export default ThemeManager;
