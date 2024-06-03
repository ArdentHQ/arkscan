import "focus-visible";
import "@ui/js/clipboard.js";
import "@ui/js/tippy.js";
import "@ui/js/page-scroll";
import "@ui/js/reposition-dropdown";

// Load images into the vite build
import.meta.glob(["../images/**"]);

import "./includes/page-scroll-handler";

import dayjs from "dayjs/esm/index.js";
import dayjsRelativeTime from "dayjs/esm/plugin/relativeTime/index.js";

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

import Wallet from "./wallet.js";
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
import ThemeManager from "./theme-manager.js";
import MobileSorting from "./mobile-sorting.js";
import TransactionsExport from "./transactions-export.js";
import Tabs from "./tabs";
import makeBlockie from "ethereum-blockies-base64";
import { truncateMiddle, TruncateDynamic } from "./truncate.js";

import "./livewire-exception-handler.js";

window.makeBlockie = makeBlockie;
window.Wallet = Wallet;
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
window.ThemeManager = ThemeManager;
window.MobileSorting = MobileSorting;
window.TransactionsExport = TransactionsExport;
window.Search = Search;
window.truncateMiddle = truncateMiddle;
window.TruncateDynamic = TruncateDynamic;

dayjs.extend(dayjsRelativeTime);

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

Livewire.start();
