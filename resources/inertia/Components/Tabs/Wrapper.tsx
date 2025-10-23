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
            popupStyles={{ width: '100%', zIndex: 20 }}
            zIndex="z-20"
            buttonClass="bg-white rounded border border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700 w-full"
            button={
                <div className="flex items-center transition-default w-full">
                    <div className="flex items-center focus:outline-none dropdown-button transition-default justify-between py-3 px-4 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-dark-50">
                        <span>{selectedTab?.text}</span>

                        <span
                            className={classNames({
                                "transition-default": true,
                                'rotate-180': isOpen,
                            })}
                        >
                            <ChevronDownSmallIcon className="w-3 h-3" />
                        </span>
                    </div>
                </div>
            }
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
            <div className="px-6 md:px-10 md:mx-auto md:max-w-7xl">
                <div className="items-center justify-between inline-flex bg-theme-secondary-100 rounded-xl dark:bg-black relative z-10 hidden mb-3 md:inline-flex">
                    <div
                        role="tablist"
                        className="flex"
                    >
                        {tabs.map((tab, index: number) => (
                            <Tab
                                key={tab.value}
                                text={tab.text}
                                value={tab.value}
                                withDivider={index > 0}
                            />
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
