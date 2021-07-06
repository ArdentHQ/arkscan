function themes() {
    const _default = {
        borderWidth: 2,
        lineTension: 0.25,
        pointRadius: 0,
    };

    const colors = {
        black: {
            dark: {
                ..._default,
                borderColor: "rgba(238,243,245,1)", // theme-secondary-200
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(238,243,245,0.5)" }, // theme-secondary-200
                        { stop: 1, value: "rgba(238,243,245,0)" }, // theme-secondary-200
                    ],
                },
            },
            light: {
                ..._default,
                borderColor: "rgba(33,34,37,1)", // theme-secondary-900
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(33,34,37,0.5)" }, // theme-secondary-900
                        { stop: 1, value: "rgba(33,34,37,0)" }, // theme-secondary-900
                    ],
                },
            },
        },

        grey: {
            dark: {
                ..._default,
                borderColor: "rgba(126,138,156,1)", // theme-secondary-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(126,138,156,1)" }, // theme-secondary-600
                        { stop: 1, value: "rgba(126,138,156,0)" }, // theme-secondary-600
                    ],
                },
            },
            light: {
                ..._default,
                borderColor: "rgba(196,200,207,1)", // theme-secondary-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(196,200,207,1)" }, // theme-secondary-400
                        { stop: 1, value: "rgba(196,200,207,0)" }, // theme-secondary-400
                    ],
                },
            },
        },

        yellow: {
            dark: {
                ..._default,
                borderColor: "rgba(255,174,16,1)", // theme-warning-500
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(255,174,16,0.5)" }, // theme-warning-500
                        { stop: 1, value: "rgba(255,174,16,0)" }, // theme-warning-500
                    ],
                },
            },
            light: {
                ..._default,
                borderColor: "rgba(255,174,16,1)", // theme-warning-500
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(255,174,16,0.5)" }, // theme-warning-500
                        { stop: 1, value: "rgba(255,174,16,0)" }, // theme-warning-500
                    ],
                },
            },
        },

        green: {
            dark: {
                ..._default,
                borderColor: "rgba(40,149,72,1)", // theme-success-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(40,149,72,0.5)" }, // theme-success-600
                        { stop: 1, value: "rgba(40,149,72,0)" }, // theme-success-600
                    ],
                },
            },
            light: {
                ..._default,
                borderColor: "rgba(40,149,72,1)", // theme-success-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(40,149,72,0.5)" }, // theme-success-600
                        { stop: 1, value: "rgba(40,149,72,0)" }, // theme-success-600
                    ],
                },
            },
        },

        red: {
            dark: {
                ..._default,
                borderColor: "rgba(222,88,70,1)", // theme-danger-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(222,88,70,0.5)" }, // theme-danger-400
                        { stop: 1, value: "rgba(222,88,70,0)" }, // theme-danger-400
                    ],
                },
            },
            light: {
                ..._default,
                borderColor: "rgba(222,88,70,1)", // theme-danger-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(222,88,70,0.5)" }, // theme-danger-400
                        { stop: 1, value: "rgba(222,88,70,0)" }, // theme-danger-400
                    ],
                },
            },
        },
    };

    return {
        grey: colors.grey,
        black: colors.black,
        yellow: colors.yellow,
        green: colors.green,
        red: colors.red,
    };
}

export function makeGradient(canvas, options) {
    const ctx = canvas.getContext("2d");
    const height = canvas.parentElement.clientHeight / 1.3;
    const gradient = ctx.createLinearGradient(0, 0, 0, height);

    options.forEach((color) => {
        gradient.addColorStop(color.stop, color.value);
    });

    return gradient;
}

export function getInfoFromThemeName(name, mode) {
    const theme = themes()[name][mode];

    return {
        backgroundColor: theme.backgroundColor,
        borderColor: theme.borderColor,
        borderWidth: theme.borderWidth,
        lineTension: theme.lineTension,
        pointRadius: theme.pointRadius,
    };
}
