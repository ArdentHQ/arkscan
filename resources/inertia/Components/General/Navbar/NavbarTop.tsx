import PriceTicker from "@/Components/General/PriceTicker/PriceTicker";
import ThemeDropdown from "@/Components/General/ThemeDropdown/ThemeDropdown";
import NetworkDropdown from "@/Components/General/NetworkDropdown/NetworkDropdown";
import NavbarSearch from "@/Components/General/NavbarSearch/NavbarSearch";
import NavbarArkConnect from "@/Components/General/NavbarArkConnect/NavbarArkConnect";
import useShareData from "@/hooks/use-shared-data";

export default function NavbarTop() {
    const { arkconnectConfig } = useShareData();

    return (
        <div className="dark:bg-theme-dark-900 relative z-40 hidden bg-white md:flex md:flex-col">
            <div className="content-container flex w-full items-center justify-between py-3">
                <div className="mr-3 flex items-center whitespace-nowrap">
                    <div className="text-theme-secondary-900 flex font-semibold dark:text-white">
                        <PriceTicker />
                    </div>
                </div>

                <div className="md-lg:w-auto flex items-center space-x-3 md:w-full">
                    <NavbarSearch />

                    <NetworkDropdown />

                    <ThemeDropdown />

                    {arkconnectConfig.enabled && <NavbarArkConnect />}
                </div>
            </div>

            <div className="border-theme-secondary-300 dark:border-theme-dark-700 absolute bottom-0 w-full border-b"></div>
        </div>
    );
}
