import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import useShareData from "@/hooks/use-shared-data";
import { useTranslation } from "react-i18next";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import classNames from "classnames";

export default function NetworkDropdown() {
    const { isProduction, mainnetExplorerUrl, testnetExplorerUrl } = useShareData();
    const { t } = useTranslation();

    return (
        <DropdownProvider>
            <Dropdown
                useDefaultButtonClasses={false}
                buttonClass="flex items-center justify-center focus:outline-none dropdown-button transition-default
p-2 space-x-1.5 h-8 text-sm font-semibold rounded
px-3
bg-white hover:bg-theme-secondary-200
border border-theme-secondary-300
text-theme-secondary-700 hover:text-theme-secondary-900
dark:bg-theme-dark-900 dark:border-theme-dark-700 dark:text-theme-dark-200 dark:hover:bg-theme-dark-700
dim:bg-theme-dark-900 dim:hover:bg-theme-dark-700"
                button={({ isOpen }) => (
                    <>
                        <span>{isProduction ? t("general.navbar.mainnet") : t("general.navbar.testnet")}</span>

                        <span className={classNames("transition-default", { "rotate-180": isOpen })}>
                            <ChevronDownSmallIcon className="h-2.5 w-2.5 md:h-3 md:w-3" />
                        </span>
                    </>
                )}
            >
                <DropdownItem selected={isProduction} asChild>
                    <a className="inline-flex items-center justify-between" href={mainnetExplorerUrl}>
                        {t("general.navbar.mainnet")}
                    </a>
                </DropdownItem>

                <DropdownItem selected={!isProduction} asChild>
                    <a className="inline-flex items-center justify-between" href={testnetExplorerUrl}>
                        {t("general.navbar.testnet")}
                    </a>
                </DropdownItem>
            </Dropdown>
        </DropdownProvider>
    );
}
