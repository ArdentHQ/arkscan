import { ITab } from "@/Providers/Tabs/types";
import Tab from "./Tab";
import Dropdown from "../General/Dropdown/Dropdown";
import DropdownItem from "../General/Dropdown/DropdownItem";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { useDropdown } from "@/Providers/Dropdown/DropdownContext";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import classNames from "@/utils/class-names";
import MobileDivider from "../General/MobileDivider";

function MobileWrapper({ tabs }: { tabs: ITab[] }) {
    const { selectedTab, select } = useTabs();
    const { isOpen } = useDropdown();

    return (
        <Dropdown
            dropdownClasses="px-6 w-full"
            popupStyles={{ width: "100%", zIndex: 20 }}
            zIndex={20}
            buttonClass="bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700 w-full"
            button={
                <div className="transition-default flex w-full items-center">
                    <div className="dropdown-button transition-default flex w-full items-center justify-between px-4 py-3 text-left font-semibold text-theme-secondary-900 focus:outline-none dark:text-theme-dark-50">
                        <span>{selectedTab?.text}</span>

                        <span
                            className={classNames({
                                "transition-default": true,
                                "rotate-180": isOpen,
                            })}
                        >
                            <ChevronDownSmallIcon className="h-3 w-3" />
                        </span>
                    </div>
                </div>
            }
            testId="tabs:dropdown"
        >
            {tabs.map((tab) => (
                <DropdownItem
                    key={tab.value}
                    onClick={() => select(tab.value)}
                    selected={selectedTab?.value === tab.value}
                >
                    {tab.text}
                </DropdownItem>
            ))}
        </Dropdown>
    );
}

export default function Wrapper({ tabs }: { tabs: ITab[] }) {
    return (
        <>
            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <div className="relative z-10 mb-3 inline-flex hidden items-center justify-between rounded-xl bg-theme-secondary-100 dark:bg-black md:inline-flex">
                    <div role="tablist" className="flex">
                        {tabs.map((tab, index: number) => (
                            <Tab key={tab.value} text={tab.text} value={tab.value} withDivider={index > 0} />
                        ))}
                    </div>
                </div>
            </div>

            <div className="md:hidden">
                <MobileDivider className="mb-6" />

                <div className="px-6">
                    <DropdownProvider>
                        <MobileWrapper tabs={tabs} />
                    </DropdownProvider>
                </div>
            </div>
        </>
    );
}
