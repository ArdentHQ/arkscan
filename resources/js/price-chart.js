import { Chart, registerables } from "chart.js";
import "chartjs-adapter-date-fns";
import { en } from "date-fns/locale";

Chart.register(...registerables);

const PriceChart = (values, labels, isPlaceholder, darkMode, isPositive) => {
    // The margin is used to not cut the line at the top/bottom
    const margin = Math.max.apply(Math, values) * 0.01;
    const maxValue = Math.max.apply(Math, values) + margin;
    const minValue = Math.min.apply(Math, values) - margin;

    return {
        darkMode: darkMode,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            this.updateChart();
        },
        init() {
            const ctx = this.$refs.chart.getContext("2d");

            const chart = Object.values(Chart.instances).find(
                (i) => i.ctx === ctx
            );

            const gradient = ctx.createLinearGradient(0, 0, 0, 40);

            let border;

            if (isPlaceholder) {
                border = this.darkMode
                    ? "rgba(126, 138, 156, 1)"
                    : "rgba(196, 200, 207, 1)";
                gradient.addColorStop(
                    0,
                    this.darkMode
                        ? "rgba(126, 138, 156, 1)"
                        : "rgba(196, 200, 207, 1)"
                );
                gradient.addColorStop(
                    1,
                    this.darkMode
                        ? "rgba(126, 138, 156, 0)"
                        : "rgba(196, 200, 207, 0)"
                );
            } else if (isPositive) {
                border = "rgba(40, 149, 72, 1)";
                gradient.addColorStop(0, "rgba(40, 149, 72, 0.5)");
                gradient.addColorStop(1, "rgba(40, 149, 72, 0)");
            } else {
                border = "rgba(222, 88, 70, 1)";
                gradient.addColorStop(0, "rgba(222, 88, 70, 0.5)");
                gradient.addColorStop(1, "rgba(222, 88, 70, 0)");
            }

            const datasets = [
                {
                    backgroundColor: gradient,
                    borderColor: border,
                    data: values,
                    pointRadius: 0,
                    borderWidth: "2",
                    lineTension: 0.25,
                    fill: true,
                },
            ];

            const data = {
                labels: labels,
                datasets,
            };

            const options = {
                animations: {
                    tension: {
                        duration: 500,
                        easing: "linear",
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        enabled: false,
                    },
                },
                scales: {
                    x: {
                        type: "time",
                        adapters: {
                            date: {
                                locale: en,
                            },
                        },
                        ticks: {
                            display: false,
                        },
                        grid: {
                            display: false,
                            tickLength: 0,
                            drawBorder: false,
                        },
                    },
                    y: {
                        ticks: {
                            display: false,
                            max: maxValue,
                            min: minValue,
                        },
                        grid: {
                            display: false,
                            tickLength: 0,
                            drawBorder: false,
                        },
                    },
                },
            };

            const config = {
                type: "line",
                data,
                options,
            };

            if (!chart) {
                new Chart(ctx, config);
            } else {
                chart.options = options;
                chart.data = data;
                chart.update();
            }
        },
    };
};

export default PriceChart;
