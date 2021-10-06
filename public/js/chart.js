import {
    getInfoFromThemeName,
    makeGradient,
    getFontConfig,
    getAxisThemeConfig,
} from "./chart-theme";

/**
 * @param {String} id
 * @param {Array} values
 * @param {Array} labels
 * @param {Boolean} grid
 * @param {Boolean} tooltips
 * @param {Array<Object{name, mode}>} theme
 * @param {Number} time
 * @param {String} currency
 * @return {Object}
 */
const CustomChart = (
    id,
    values,
    labels,
    grid,
    tooltips,
    theme,
    time,
    currency
) => {
    return {
        time: time,
        chart: null,
        currency: currency || "USD",

        getCanvas() {
            return this.$refs[id];
        },

        getCanvasContext() {
            return this.getCanvas().getContext("2d");
        },

        getRangeFromValues(values, margin = 0) {
            const max = Math.max.apply(Math, values);
            const min = Math.min.apply(Math, values);
            const _margin = max * margin;

            return {
                min: min - _margin,
                max: max + _margin,
            };
        },

        getCurrencyValue(value) {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: this.currency,
            }).format(value);
        },

        resizeChart() {
            this.updateChart();
        },

        updateChart() {
            this.chart.datasets = this.loadData();
            this.chart.labels = labels;
            this.chart.update();
        },

        loadData() {
            const datasets = [];

            if (values.length === 0) {
                values = [0, 0];
                labels = [0, 1];
            }

            if (Array.isArray(values) && !values[0].hasOwnProperty("data")) {
                values = [values];
            }

            values.forEach((value, key) => {
                let themeName = value.type === "bar" ? "grey" : theme.name;
                let graphic = getInfoFromThemeName(themeName, theme.mode);
                let backgroundColor = graphic.backgroundColor;
                if (backgroundColor.hasOwnProperty("gradient")) {
                    backgroundColor = makeGradient(
                        this.getCanvas(),
                        backgroundColor.gradient
                    );
                }

                datasets.push({
                    stack: "combined",
                    label: value.name || "",
                    data: value.data || value,
                    type: value.type || "line",
                    backgroundColor:
                        value.type === "bar"
                            ? graphic.borderColor
                            : backgroundColor,
                    borderColor:
                        value.type === "bar"
                            ? "transparent"
                            : graphic.borderColor,
                    borderWidth:
                        value.type === "bar"
                            ? "transparent"
                            : graphic.borderWidth,
                    cubicInterpolationMode: "monotone",
                    tension: graphic.lineTension,
                    pointRadius: graphic.pointRadius,
                    pointBackgroundColor: graphic.pointBackgroundColor,
                    pointHoverRadius: tooltips ? graphic.pointHoverRadius : 0,
                    pointHoverBorderWidth: tooltips
                        ? graphic.pointHoverBorderWidth
                        : 0,
                    pointHoverBorderColor: tooltips ? graphic.borderColor : 0,
                    pointHoverBackgroundColor: tooltips
                        ? graphic.pointHoverBackgroundColor
                        : 0,
                });
            });

            return datasets;
        },

        loadYAxes() {
            const axes = [];

            values.forEach((value, key) => {
                let range = this.getRangeFromValues(value, 0.01);
                axes.push({
                    display: grid && key === 0,
                    type: "linear",
                    position: "right",
                    ticks: {
                        ...getFontConfig("axis", theme.mode),
                        padding: 15,
                        display: grid && key === 0,
                        suggestedMax: range.max,
                        callback: (value, index, data) =>
                            this.getCurrencyValue(value),
                    },
                    gridLines: {
                        display: grid && key === 0,
                        drawBorder: false,
                        color: getAxisThemeConfig(theme.mode).y.color,
                    },
                });
            });

            return axes;
        },

        init() {
            if (this.chart) {
                this.chart.destroy();
            }

            this.$watch("time", () => this.updateChart());
            document.addEventListener("resize", () =>
                window.livewire.emit("updateChart")
            );

            const data = {
                labels: labels,
                datasets: this.loadData(),
            };

            const options = {
                spanGaps: true,
                parsing: false,
                normalized: true,
                responsive: true,
                maintainAspectRatio: false,
                showScale: grid,
                animation: {duration: 300, easing: "easeOutQuad"},
                legend: {display: false},
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0,
                    },
                },
                hover: {
                    mode: "nearest",
                    intersect: false,
                    axis: "x",
                },
                tooltips: {
                    enabled: tooltips,
                    mode: "nearest",
                    intersect: false,
                    axis: "x",
                    external: this.tooltip,
                    displayColors: false,
                    stacked: false,
                    callbacks: {
                        title: (items) => {
                        },
                        label: (context) =>
                            this.getCurrencyValue(context.value),
                        labelTextColor: (context) =>
                            getFontConfig("tooltip", theme.mode).fontColor,
                    },
                    backgroundColor: getFontConfig("tooltip", theme.mode)
                        .backgroundColor,
                },
                scales: {
                    yAxes: this.loadYAxes(),
                    xAxes: [
                        {
                            display: grid,
                            type: "category",
                            labels: labels,
                            ticks: {
                                display: grid,
                                includeBounds: true,
                                padding: 10,
                                ...getFontConfig("axis", theme.mode),
                            },
                            gridLines: {
                                display: grid,
                                drawBorder: false,
                                color: getAxisThemeConfig(theme.mode).x.color,
                            },
                        },
                    ],
                },
            };

            this.chart = new Chart(this.getCanvasContext(), {data, options});
        },
    };
};

export default CustomChart;
