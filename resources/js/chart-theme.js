function themes() {
    const _default = {
        borderWidth: 2,
        lineTension: 0.25,
        pointRadius: 0,
        pointHoverRadius: 6,
        pointHoverBorderWidth: 2,
    };

    return {
        black: {
            dark: {
                ..._default,
                borderColor: "rgba(238,243,245,1)", // theme-secondary-200
                pointHoverBorderColor: "rgba(238,243,245,1)", // theme-secondary-200
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(238,243,245,0.5)" }, // theme-secondary-200
                        { stop: 1, value: "rgba(238,243,245,0)" }, // theme-secondary-200
                    ],
                },
                pointBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
            light: {
                ..._default,
                borderColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBorderColor: "rgba(33,34,37,1)", // theme-secondary-900
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(33,34,37,0.5)" }, // theme-secondary-900
                        { stop: 1, value: "rgba(33,34,37,0)" }, // theme-secondary-900
                    ],
                },
                pointBackgroundColor: "rgba(255,255,255,1)", // theme-white
                pointHoverBackgroundColor: "rgba(255,255,255,1)", // theme-white
            },
        },

        grey: {
            dark: {
                ..._default,
                borderColor: "rgba(126,138,156,1)", // theme-secondary-600
                pointHoverBorderColor: "rgba(126,138,156,1)", // theme-secondary-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(126,138,156,1)" }, // theme-secondary-600
                        { stop: 1, value: "rgba(126,138,156,0)" }, // theme-secondary-600
                    ],
                },
                pointBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
            light: {
                ..._default,
                borderColor: "rgba(196,200,207,1)", // theme-secondary-400
                pointHoverBorderColor: "rgba(196,200,207,1)", // theme-secondary-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(196,200,207,1)" }, // theme-secondary-400
                        { stop: 1, value: "rgba(196,200,207,0)" }, // theme-secondary-400
                    ],
                },
                pointBackgroundColor: "rgba(255,255,255,1)", // theme-white
                pointHoverBackgroundColor: "rgba(255,255,255,1)", // theme-white
            },
        },

        yellow: {
            dark: {
                ..._default,
                borderColor: "rgba(255,174,16,1)", // theme-warning-500
                pointHoverBorderColor: "rgba(255,174,16,1)", // theme-warning-500
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(255,174,16,0.5)" }, // theme-warning-500
                        { stop: 1, value: "rgba(255,174,16,0)" }, // theme-warning-500
                    ],
                },
                pointBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
            light: {
                ..._default,
                borderColor: "rgba(255,174,16,1)", // theme-warning-500
                pointHoverBorderColor: "rgba(255,174,16,1)", // theme-warning-500
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(255,174,16,0.5)" }, // theme-warning-500
                        { stop: 1, value: "rgba(255,174,16,0)" }, // theme-warning-500
                    ],
                },
                pointBackgroundColor: "rgba(255,255,255,1)", // theme-white
                pointHoverBackgroundColor: "rgba(255,255,255,1)", // theme-white
            },
        },

        green: {
            dark: {
                ..._default,
                borderColor: "rgba(40,149,72,1)", // theme-success-600
                pointHoverBorderColor: "rgba(40,149,72,1)", // theme-success-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(40,149,72,0.5)" }, // theme-success-600
                        { stop: 1, value: "rgba(40,149,72,0)" }, // theme-success-600
                    ],
                },
                pointBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
            light: {
                ..._default,
                borderColor: "rgba(40,149,72,1)", // theme-success-600
                pointHoverBorderColor: "rgba(40,149,72,1)", // theme-success-600
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(40,149,72,0.5)" }, // theme-success-600
                        { stop: 1, value: "rgba(40,149,72,0)" }, // theme-success-600
                    ],
                },
                pointBackgroundColor: "rgba(255,255,255,1)", // theme-white
                pointHoverBackgroundColor: "rgba(255,255,255,1)", // theme-white
            },
        },

        red: {
            dark: {
                ..._default,
                borderColor: "rgba(222,88,70,1)", // theme-danger-400
                pointHoverBorderColor: "rgba(222,88,70,1)", // theme-danger-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(222,88,70,0.5)" }, // theme-danger-400
                        { stop: 1, value: "rgba(222,88,70,0)" }, // theme-danger-400
                    ],
                },
                pointBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
                pointHoverBackgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
            light: {
                ..._default,
                borderColor: "rgba(222,88,70,1)", // theme-danger-400
                pointHoverBorderColor: "rgba(222,88,70,1)", // theme-danger-400
                backgroundColor: {
                    gradient: [
                        { stop: 0, value: "rgba(222,88,70,0.5)" }, // theme-danger-400
                        { stop: 1, value: "rgba(222,88,70,0)" }, // theme-danger-400
                    ],
                },
                pointBackgroundColor: "rgba(255,255,255,1)", // theme-white
                pointHoverBackgroundColor: "rgba(255,255,255,1)", // theme-white
            },
        },
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
    return themes()[name][mode];
}

export function getFontConfig(type, mode) {
    const _default = {
        axis: {
            fontSize: 14,
            fontStyle: 600,
        },
    };

    const config = {
        axis: {
            light: {
                ..._default.axis,
                fontColor: "rgba(165,173,185,1)", // theme-secondary-500
            },
            dark: {
                ..._default.axis,
                fontColor: "rgba(99,114,130,1)", // theme-secondary-700
            },
        },
        tooltip: {
            light: {
                fontColor: "rgba(99,114,130,1)", // theme-secondary-700
                backgroundColor: "rgba(255,255,255,1)", // theme-white
            },
            dark: {
                fontColor: "rgba(165,173,185,1)", // theme-secondary-500
                backgroundColor: "rgba(33,34,37,1)", // theme-secondary-900
            },
        },
    };

    return config[type][mode];
}

export function getAxisThemeConfig(mode) {
    const config = {
        light: {
            x: {
                color: "rgba(238,243,245,1)", // theme-secondary-200
            },
            y: {
                color: "rgba(238,243,245,1)", // theme-secondary-200
            },
        },
        dark: {
            x: {
                color: "rgba(60,66,73,1)", // theme-secondary-800
            },
            y: {
                color: "rgba(60,66,73,1)", // theme-secondary-800
            },
        },
    };

    return config[mode];
}
