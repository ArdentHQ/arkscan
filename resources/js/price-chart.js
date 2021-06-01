const PriceChart = (values, labels, isPlaceholder, darkMode, time) => {
    // The margin is used to not cut the line at the top/bottom
    const margin = Math.max.apply(Math, values) * 0.01;
    const maxValue = Math.max.apply(Math, values) + margin;
    const minValue = Math.min.apply(Math, values) - margin;
    const isPositive = isPlaceholder
        ? null
        : parseFloat(values[values.length - 1]) >= parseFloat(values[0]);

    return {
        time: time,
        darkMode: darkMode,
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            this.updateChart();
        },
        updateChart() {
            const ctx = this.$refs.chart.getContext("2d");
            const chart = Object.values(Chart.instances).find(
                (i) => i.ctx === ctx
            );
            this.init(chart);
        },
        init(chart = null) {
            const ctx = this.$refs.chart.getContext("2d");

            if (chart === null) {
                this.$watch("time", () => this.updateChart());
            }

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
                },
            ];

            const data = {
                labels: labels,
                datasets,
            };

            if (chart) {
                data.labels.forEach((label, index) => {
                    chart.data.labels.splice(index, 1, label);
                });

                data.datasets[0].data.forEach((value, index) => {
                    chart.data.datasets[0].data.splice(index, 1, value);
                });

                chart.data.datasets[0].backgroundColor = gradient;
                chart.data.datasets[0].borderColor = border;

                chart.options.scales.yAxes[0].ticks.max = maxValue;
                chart.options.scales.yAxes[0].ticks.min = minValue;

                chart.update();

                return;
            }

            const options = {
                animation: {
                    duration: 500,
                    easing: "linear",
                },
                tooltips: {
                    enabled: false,
                },
                legend: {
                    display: false,
                },
                scales: {
                    xAxes: [
                        {
                            type: "time",
                            ticks: {
                                display: false,
                            },
                            gridLines: {
                                display: false,
                                tickMarkLength: 0,
                            },
                        },
                    ],
                    yAxes: [
                        {
                            ticks: {
                                display: false,
                                max: maxValue,
                                min: minValue,
                            },
                            gridLines: {
                                display: false,
                                tickMarkLength: 0,
                            },
                        },
                    ],
                },
            };

            const config = {
                type: "line",
                data,
                options,
            };

            new Chart(ctx, config);
        },
    };
};

export default PriceChart;
