import { useEffect, useMemo, useRef } from "react";
import CustomChart from "@ui/js/chart.js";
import "chartjs-adapter-date-fns";
import "@js/chart-tooltip";

export interface ChartTheme {
    name: string;
    mode: string | null;
}

export default function ChartCanvas({
    id,
    datasets,
    labels,
    theme,
    currency,
    className = "",
    canvasClassName = "",
    height,
    width,
    grid = true,
    tooltips = true,
    showCrosshair = true,
    hasDateTimeLabels = true,
    yPadding = 10,
    xPadding = 0,
    dateUnitOverride = null,
}: {
    id: string;
    datasets: number[];
    labels: Array<number | string>;
    theme: ChartTheme;
    currency: string;
    className?: string;
    canvasClassName?: string;
    height?: number;
    width?: number | null;
    grid?: boolean;
    tooltips?: boolean;
    showCrosshair?: boolean;
    hasDateTimeLabels?: boolean;
    yPadding?: number;
    xPadding?: number;
    dateUnitOverride?: string | null;
}) {
    const canvasRef = useRef<HTMLCanvasElement | null>(null);
    const chartRef = useRef<ReturnType<typeof CustomChart> | null>(null);

    const chartLabels = useMemo(
        () => (hasDateTimeLabels ? labels.map((label) => new Date(Number(label) * 1000)) : labels),
        [labels, hasDateTimeLabels],
    );

    useEffect(() => {
        if (!canvasRef.current) {
            return;
        }

        const tooltipHandler = tooltips ? (window.chartTooltip ?? null) : null;
        const chart = CustomChart(
            id,
            datasets,
            chartLabels,
            grid,
            tooltips,
            theme,
            Date.now(),
            currency,
            yPadding,
            xPadding,
            showCrosshair,
            tooltipHandler,
            hasDateTimeLabels,
            dateUnitOverride,
        );

        chart.$refs = { [id]: canvasRef.current };
        chart.$watch = () => undefined;
        chart.init();

        chartRef.current = chart;

        return () => {
            if (chartRef.current?.chart) {
                chartRef.current.chart.destroy();
            }
        };
    }, [
        id,
        datasets,
        chartLabels,
        grid,
        tooltips,
        theme,
        currency,
        yPadding,
        xPadding,
        showCrosshair,
        hasDateTimeLabels,
        dateUnitOverride,
    ]);

    return (
        <div className={className}>
            <div className="relative h-full w-full">
                <canvas
                    ref={canvasRef}
                    id={id}
                    className={canvasClassName}
                    height={height}
                    width={width ?? undefined}
                />
            </div>
        </div>
    );
}
