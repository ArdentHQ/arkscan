import "focus-visible";
import "@ui/js/tippy.js";
import "@ui/js/page-scroll";
import "@ui/js/reposition-dropdown";

import "./includes/page-scroll-handler";

import * as dayjs from "dayjs";
import * as dayjsRelativeTime from "dayjs/plugin/relativeTime";

// @see https://laravel-mix.com/docs/6.0/upgrade#unused-library-extraction
import Alpine from "alpinejs";
import BlocksExport from "./blocks-export.js";
import { Chart } from "chart.js";
import CustomChart from "@ui/js/chart.js";
import Dropdown from "@ui/js/dropdown.js";
import Delegate from "./delegate.js";
import Modal from "@ui/js/modal";
import Navbar from "@ui/js/navbar";
import Pagination from "@ui/js/pagination";
import Pikaday from "pikaday";
import PriceChart from "./price-chart";
import ReadMore from "@ui/js/read-more.js";
import RichSelect from "./rich-select.js";
import Search from "./search";
import TableSorting from "./table-sorting.js";
import MobileSorting from "./mobile-sorting.js";
import TransactionsExport from "./transactions-export.js";
import Tabs from "./tabs";
import makeBlockie from "ethereum-blockies-base64";

import "./livewire-exception-handler.js";

window.makeBlockie = makeBlockie;
window.Alpine = Alpine;
window.BlocksExport = BlocksExport;
window.Chart = Chart;
window.Dropdown = Dropdown;
window.Delegate = Delegate;
window.dayjs = dayjs;
window.Tabs = Tabs;
window.Pikaday = Pikaday;
window.Pagination = Pagination;
window.Modal = Modal;
window.ReadMore = ReadMore;
window.RichSelect = RichSelect;
window.PriceChart = PriceChart;
window.Navbar = Navbar;
window.CustomChart = CustomChart;
window.CustomChart = CustomChart;
window.TableSorting = TableSorting;
window.MobileSorting = MobileSorting;
window.TransactionsExport = TransactionsExport;
window.Search = Search;

dayjs.extend(dayjsRelativeTime);

Alpine.start();

/**
 * If browser back button was used, flush cache
 * This ensures that user will always see an accurate, up-to-date view based on their state
 * https://stackoverflow.com/questions/8788802/prevent-safari-loading-from-cache-when-back-button-is-clicked
 */
window.onpageshow = function (event) {
    if (event.persisted) {
        window.location.reload();
    }
};

window.hideTableTooltipsOnLivewireEvent = (regex) => {
    Livewire.hook("message.processed", (message, component) => {
        if (!regex.test(component.name)) {
            return;
        }

        window.hideAllTooltips();
    });
};
