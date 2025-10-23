import dayjs from "dayjs/esm/index.js";
import customParseFormat from "dayjs/esm/plugin/customParseFormat/index.js";

dayjs.extend(customParseFormat);

window.chartTooltip = (context) => {
    const { chart, tooltip } = context;
    let tooltipEl = chart.canvas.parentNode.querySelector("div");

    const getCurrencyValue = (value) => {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: chart.options.currency,
        }).format(value);
    };

    if (!tooltipEl) {
        tooltipEl = document.createElement("div");
        tooltipEl.classList.add(
            "chart-custom-tooltip",
            "bg-theme-secondary-900",
            "dark:bg-theme-dark-800",
            "rounded",
            "absolute",
            "text-white",
            "leading-3.75",
            "text-left",
            "p-2",
        );

        tooltipEl.style.opacity = 1;
        tooltipEl.style.pointerEvents = "none";
        tooltipEl.style.position = "absolute";
        tooltipEl.style.transform = "translate(-50%, 0)";
        tooltipEl.style.transition = "all .1s ease";

        const table = document.createElement("table");
        table.style.margin = "0px";

        tooltipEl.appendChild(table);
        chart.canvas.parentNode.appendChild(tooltipEl);
    }

    if (tooltip.opacity === 0) {
        tooltipEl.style.opacity = 0;
        return;
    }

    const titleLines = tooltip.title || [];
    const dataValue = tooltip.dataPoints[0].dataset.data[tooltip.dataPoints[0].dataIndex];

    const tableHead = document.createElement("thead");
    const heading = document.createElement("span");
    heading.innerHTML = "Price:";
    heading.classList.add("mr-1", "font-semibold", "text-theme-secondary-500", "dark:text-theme-dark-200", "text-xs");

    const tr = document.createElement("tr");
    tr.style.backgroundColor = "inherit";
    tr.style.borderWidth = 0;

    const th = document.createElement("th");
    th.style.borderWidth = 0;

    const text = document.createElement("span");
    text.innerHTML = getCurrencyValue(dataValue);
    text.classList.add("font-semibold", "text-xs", "dark:text-theme-dark-50");

    th.appendChild(heading);
    th.appendChild(text);
    tr.appendChild(th);
    tableHead.appendChild(tr);

    const tableBody = document.createElement("tbody");

    titleLines.forEach((dateString) => {
        const tr = document.createElement("tr");
        tr.style.borderWidth = 0;

        const td = document.createElement("td");
        td.style.borderWidth = 0;
        td.classList.add("pt-1.5");

        const date = dayjs(dateString.replace("p.m.", "pm"), "MMM D, YYYY, H:mm:ss a");
        const text = document.createElement("span");
        text.innerHTML = date.format("D MMM YYYY HH:mm:ss");
        text.classList.add(
            "font-semibold",
            "text-theme-secondary-500",
            "dark:text-theme-dark-200",
            "text-xs",
            "whitespace-nowrap",
        );

        td.appendChild(text);
        tr.appendChild(td);
        tableBody.appendChild(tr);
    });

    const tableRoot = tooltipEl.querySelector("table");

    // Remove old children
    while (tableRoot.firstChild) {
        tableRoot.firstChild.remove();
    }

    // Add new children
    tableRoot.appendChild(tableHead);
    tableRoot.appendChild(tableBody);

    const { offsetLeft: positionX, offsetTop: positionY } = chart.canvas;

    // Display, position, and set styles for font
    tooltipEl.style.opacity = 1;
    tooltipEl.style.left = positionX + tooltip.caretX + "px";
    tooltipEl.style.top = positionY + tooltip.caretY - tooltipEl.clientHeight - 16 + "px";
};
