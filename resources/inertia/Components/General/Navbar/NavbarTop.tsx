import PriceTicker from "@/Components/General/PriceTicker/PriceTicker";
import ThemeDropdown from "@/Components/General/ThemeDropdown/ThemeDropdown";

export default function NavbarTop() {
    return (
        <div className="relative z-40 hidden bg-white dark:bg-theme-dark-900 md:flex md:flex-col">
            <div className="content-container flex w-full items-center justify-between py-3">
                <div className="mr-3 flex items-center whitespace-nowrap">
                    <div className="flex font-semibold text-theme-secondary-900 dark:text-white">
                        <PriceTicker />
                    </div>
                </div>

                <div className="flex items-center space-x-3 md:w-full md-lg:w-auto">
                    {/* <livewire:navbar.search /> *}

            {/* <x-navbar.network-dropdown /> */}

                    <ThemeDropdown />

                    {/* <div
                x-data="ThemeManager()"
                @theme-changed.window="theme = $event.detail.theme"
            >
                <x-navbar.theme-dropdown />
            </div> */}

                    {/* @if (config('arkscan.arkconnect.enabled', false))
                <x-navbar.arkconnect />
            @endif */}
                </div>
            </div>

            <div className="absolute bottom-0 w-full border-b border-theme-secondary-300 dark:border-theme-dark-700"></div>
        </div>
    );
}
