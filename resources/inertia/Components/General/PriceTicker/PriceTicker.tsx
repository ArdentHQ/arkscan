import useConfig from "@/hooks/use-config";
import { useMemo } from "react";
import classNames from "@/utils/class-names";
import { useTranslation } from "react-i18next";
import { currencyWithDecimals } from "@/utils/number-formatter";
import useCurrency from "@/Providers/Currency/useCurrency";
import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";

export default function PriceTicker() {
    const { isDownForMaintenance, network, isPriceAvailable, priceExchangeRate, networkName, currencies } = useConfig();

    const isDisabled = useMemo(() => {
        return isDownForMaintenance || !network.canBeExchanged || networkName !== "production" || !isPriceAvailable;
    }, [isDownForMaintenance, network.canBeExchanged, isPriceAvailable, networkName]);

    const { currency, updateCurrency, isUpdatingCurrency } = useCurrency();

    const { t } = useTranslation();

    return (
        <div
            // @if (! $isDisabled && config('broadcasting.default') !== 'reverb')
            //     wire:poll.visible.30s
            // @endif
            className={classNames("w-full md:w-auto", { "opacity-50": isUpdatingCurrency })}
        >
            <div
                className={classNames(
                    "flex items-center justify-between rounded border-theme-secondary-300 dark:border-transparent md:border md:bg-theme-secondary-200 md:dark:bg-theme-dark-700",
                    {
                        "cursor-not-allowed select-none dark:text-theme-dark-200 md:text-theme-secondary-500 md:dark:text-theme-dark-500":
                            isDisabled,
                        "cursor-default dark:text-theme-dark-200 md:text-theme-secondary-700 md:dark:text-theme-dark-50":
                            !isDisabled,
                    },
                )}
            >
                <div className="transition-default font-semibold md:py-1.5 md:pl-3 md:pr-2 md:text-sm">
                    <span>{t("general.navbar.price")}:&nbsp;</span>
                    {isDisabled ? (
                        t("general.na")
                    ) : (
                        <div className="inline-flex items-center">
                            {currencyWithDecimals(priceExchangeRate ?? 0, currency, 2)}
                        </div>
                    )}
                </div>

                <DropdownProvider>
                    <Dropdown
                        wrapperClass="relative"
                        buttonClass="rounded-r rounded-l md:bg-white md:rounded-l-none bg-theme-secondary-200 text-theme-secondary-700 dim:hover:bg-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-600 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dark:hover:bg-theme-secondary-800 dark:text-theme-dark-200 hover:bg-theme-secondary-200"
                        dropdownClasses="right-0 min-w-[160px]"
                        disabled={isDisabled}
                        button={({ isOpen }) => (
                            <div
                                className={classNames(
                                    "transition-default group flex items-center justify-center space-x-2 py-2 pr-3 text-sm font-semibold leading-4",
                                    {
                                        "cursor-not-allowed": isDisabled,
                                        "hover:text-theme-secondary-900 dark:text-theme-dark-50 hover:dark:text-theme-dark-50 md:dark:text-theme-dark-50":
                                            !isDisabled,
                                    },
                                )}
                            >
                                <div
                                    className={classNames("h-3.5 md:block md:w-px", {
                                        "bg-theme-secondary-300 dark:bg-theme-dark-500": isDisabled,
                                        "bg-transparent md:group-hover:bg-theme-secondary-300 md:group-hover:dark:bg-theme-dark-700":
                                            !isDisabled,
                                    })}
                                ></div>

                                <span>{currency}</span>

                                <span className={classNames("transition-default", { "rotate-180": isOpen })}>
                                    <ChevronDownSmallIcon className="h-2.5 w-2.5 md:h-3 md:w-3" />
                                </span>
                            </div>
                        )}
                    >
                        <div className="custom-scroll flex h-full max-h-[246px] flex-col overflow-y-scroll overscroll-contain md:max-h-[332px] md-lg:pr-0.5">
                            {Object.values(currencies).map((item) => (
                                <DropdownItem
                                    selected={item.currency === currency}
                                    onClick={() => {
                                        updateCurrency(item.currency);
                                    }}
                                >
                                    {item.currency}

                                    {item.symbol !== null && (
                                        <span className="text-theme-secondary-500 dark:text-theme-dark-200">
                                            ({item.symbol})
                                        </span>
                                    )}
                                </DropdownItem>
                            ))}
                        </div>
                    </Dropdown>
                </DropdownProvider>
            </div>
        </div>
    );
}
