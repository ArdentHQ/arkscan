import useConfig from "@/hooks/use-config";
import { useMemo } from "react";
import classNames from "@/utils/class-names";
import { useTranslation } from "react-i18next";

export default function PriceTicker() {
    const { isDownForMaintenance, network, isPriceAvailable } = useConfig();

    const isDisabled = useMemo(() => {
        return isDownForMaintenance || !network.canBeExchanged || network.name !== "production" || !isPriceAvailable;
    }, [isDownForMaintenance, network.canBeExchanged, isPriceAvailable]);

    const { t } = useTranslation();
    // $navigation = [
    //     ['route' => 'home', 'label' => trans('menus.home')],
    //     ['label' => trans('menus.blockchain'), 'children' => [
    //         ['route' => 'blocks',           'label' => trans('menus.blocks')],
    //         ['route' => 'transactions',     'label' => trans('menus.transactions')],
    //         ['route' => 'validators',        'label' => trans('menus.validators')],
    //         ['route' => 'top-accounts',     'label' => trans('menus.top_accounts')],
    //         ['route' => 'statistics',       'label' => trans('menus.statistics')],
    //     ]],
    //     ['label' => trans('menus.resources'), 'children' => [
    //         ['route' => 'validator-monitor', 'label' => trans('menus.validator_monitor')],
    //         ['route' => 'compatible-wallets',  'label' => trans('menus.wallets')],
    //     ]],
    //     ['label' => trans('menus.developers'), 'children' => [
    //         ['url' => trans('urls.docs.arkscan'),  'label' => trans('menus.docs')],
    //         ['url' => trans('urls.docs.api'),  'label' => trans('menus.api')],
    //         ['url' => trans('urls.github'),  'label' => trans('menus.github')],
    //     ]],
    // ];

    // if (Network::canBeExchanged()) {
    //     $navigation[2]['children'][] = ['route' => 'exchanges',  'label' => trans('menus.exchanges')];
    // }

    // if (config('arkscan.support.enabled') === true) {
    //     $navigation[3]['children'][] = ['route' => 'contact', 'label' => trans('menus.support')];
    // }

    const busy = false;

    return (
        <div
            // @if (! $isDisabled && config('broadcasting.default') !== 'reverb')
            //     wire:poll.visible.30s
            // @endif
            className={classNames("w-full md:w-auto", { "opacity-50": busy })}
            // x-data="{ busy: false }"
            // x-init="Livewire.on('currencyChanged', () => busy = true);"
            // @has-loaded-price-data="busy = false"
        >
            <div
                className={classNames(
                    "'flex justify-between' items-center rounded border-theme-secondary-300 dark:border-transparent md:border md:bg-theme-secondary-200 md:dark:bg-theme-dark-700",
                    {
                        "cursor-not-allowed select-none dark:text-theme-dark-200 md:text-theme-secondary-500 md:dark:text-theme-dark-500":
                            isDisabled,
                        "cursor-default dark:text-theme-dark-200 md:text-theme-secondary-700 md:dark:text-theme-dark-50":
                            !isDisabled,
                    },
                )}
            >
                <div className="transition-default font-semibold md:py-1.5 md:pl-3 md:pr-2 md:text-sm">
                    <span>{t("general.navbar.price")}:</span>
                    {isDisabled ? (
                        t("general.na")
                    ) : (
                        <div className="inline-flex items-center">
                            {/* ExplorerNumberFormatter::currency($price, Settings::currency()) */}
                        </div>
                    )}
                </div>

                {/* <x-general.dropdown.dropdown
            active-button-className=""
            button-className="rounded-r rounded-l md:bg-white md:rounded-l-none bg-theme-secondary-200 text-theme-secondary-700 dim:hover:bg-theme-dark-700 md:dark:bg-theme-dark-900 md:dark:text-theme-dark-600 md:hover:text-theme-secondary-900 dark:bg-theme-dark-800 dark:hover:bg-theme-secondary-800 dark:text-theme-dark-200 hover:bg-theme-secondary-200"
            dropdown-className="right-0 min-w-[160px]"
            scroll-className=""
            :disabled="$isDisabled"
            dropdown-background="bg-white dark:bg-theme-dark-900 border border-white dark:border-theme-dark-700 py-[0.125rem]"
            dropdown-padding=""
            content-className=""
        >
            <x-slot
                name="button"
                className="rounded-r rounded-l md:rounded-l-none"
            >
                <div
                    @class([
                        'flex justify-center items-center py-2 pr-3 space-x-2 text-sm font-semibold leading-4 group transition-default',
                        'cursor-not-allowed' => $isDisabled,
                        'dark:text-theme-dark-50 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50 md:dark:text-theme-dark-50' => ! $isDisabled,
                    ])
                    @if ($isDisabled)
                        disabled
                    @endif
                >
                    <div @class([
                        'md:w-px h-3.5 md:block',
                        'bg-theme-secondary-300 dark:bg-theme-dark-500' => $isDisabled,
                        'bg-transparent md:group-hover:bg-theme-secondary-300 md:group-hover:dark:bg-theme-dark-700' => ! $isDisabled,
                    ])></div>

                    <span>
                        {{ $to }}
                    </span>

                    <span
                        className="transition-default"
                        :className="{ 'rotate-180': dropdownOpen }"
                    >
                        <x-ark-icon
                            name="arrows.chevron-down-small"
                            size="w-2.5 h-2.5 md:w-3 md:h-3"
                        />
                    </span>
                </div>
            </x-slot>

            <div className="flex overflow-y-scroll overscroll-contain flex-col pr-1 pl-1 h-full custom-scroll max-h-[246px] md-lg:pr-0.5 md:max-h-[332px]">
                @foreach (config('currencies.currencies') as $currency)
                    <x-general.dropdown.list-item
                        :is-active="$currency['currency'] === $to"
                        wire:click="setCurrency('{{ $currency['currency'] }}')"
                        className="inline-flex justify-between items-center"
                    >
                        <div>
                            {{ $currency['currency'] }}

                            @if ($currency['symbol'] !== null)
                                <span className="text-theme-secondary-500 dark:text-theme-dark-200">
                                    ({{ $currency['symbol'] }})
                                </span>
                            @endif
                        </div>

                        @if ($currency['currency'] === $to)
                            <span>
                                <x-ark-icon
                                    name="double-check-mark"
                                    size="sm"
                                    className="text-theme-primary-600 dark:text-theme-dark-50"
                                />
                            </span>
                        @endif
                    </x-general.dropdown.list-item>
                @endforeach
            </div>
        </x-general.dropdown> */}
            </div>
        </div>
    );
}
