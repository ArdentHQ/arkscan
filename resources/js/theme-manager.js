const ThemeManager = (xData = {}) => {
    return Alpine.reactive({
        set theme(value) {
            this.setTheme(value);
        },

        get theme() {
            return localStorage.theme;
        },

        setTheme(value) {
            document.dispatchEvent(new CustomEvent("setThemeMode", {
                detail: {
                    theme: value,
                },
            }))
        },

        ...xData,
    });
};

export default ThemeManager;
