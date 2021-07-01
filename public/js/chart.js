import { getInfoFromThemeName, makeGradient } from "./chart-theme";

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
        themeMode: theme.mode,

        getCanvas() {
            return this.$refs[id];
        },

        getCanvasContext() {
            return this.getCanvas().getContext("2d");
        },

        getFontConfig() {
            return {
                fontSize: 14,
                fontStyle: 600,
                fontColor: "#B0B0B8",
            };
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
                let graphic = getInfoFromThemeName(themeName, this.themeMode);
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
                    lineTension: graphic.lineTension,
                    pointRadius: graphic.pointRadius,
                });
            });

            return datasets;
        },

        loadYAxes() {
            const axes = [];

            const fontConfig = this.getFontConfig();

            values.forEach((value, key) => {
                let range = this.getRangeFromValues(value, 0.01);

                axes.push({
                    type: "linear",
                    position: "right",
                    stacked: true,
                    ticks: {
                        ...fontConfig,
                        padding: 15,
                        display: grid === "true" && key === 0,
                        suggestedMax: range.max,
                        callback: (value, index, data) =>
                            this.getCurrencyValue(value),
                    },
                    gridLines: {
                        display: grid === "true" && key === 0,
                        drawBorder: false,
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
            window.addEventListener("resize", () => this.resizeChart());

            const fontConfig = this.getFontConfig();

            const data = {
                labels: labels,
                datasets: this.loadData(),
            };

            const yAxes = this.loadYAxes();

            const options = {
                parsing: false,
                normalized: true,
                responsive: true,
                maintainAspectRatio: false,
                showScale: grid === "true",
                animation: { duration: 300, easing: "easeOutQuad" },
                legend: { display: false },
                onResize: () => this.resizeChart(),
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0,
                    },
                },
                scales: {
                    xAxes: [
                        {
                            type: "category",
                            labels: labels,
                            ticks: {
                                display: grid === "true",
                                includeBounds: true,
                                padding: 10,
                                ...fontConfig,
                            },
                            gridLines: {
                                display: grid === "true",
                                drawBorder: false,
                            },
                        },
                    ],
                    yAxes: yAxes,
                },
            };

            this.chart = new Chart(this.getCanvasContext(), { data, options });
        },
    };
};

export default CustomChart;
