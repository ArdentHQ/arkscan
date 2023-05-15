@php
    $isProduction = config('explorer.network') === 'production';

    $itemClass = 'border-l-4 pl-5 pr-6 py-3 font-semibold hover:text-theme-secondary-900 hover:bg-theme-secondary-200 dark:hover:bg-theme-secondary-900 dark:text-theme-secondary-200 transition-default';
    $inactiveClass = 'border-transparent';
    $activeClass = 'border-theme-primary-600 bg-theme-primary-50 dark:bg-theme-secondary-900 text-theme-secondary-900 dark:text-white';
@endphp

<div
    x-data="{
        isOpen: false,
    }"
    class="relative"
>
    <button
        @click="isOpen = !isOpen"
        @class([
            'flex justify-center items-center h-8 rounded transition-default p-2 text-sm font-semibold space-x-1.5 md:border md:w-8',
            'text-theme-secondary-700 bg-theme-secondary-200 md:bg-white md:border-theme-secondary-300 md:hover:bg-theme-secondary-200',
            'dark:text-theme-secondary-200 dark:bg-theme-secondary-800 md:dark:text-theme-secondary-600 md:dark:bg-theme-secondary-900 md:dark:border-theme-secondary-800 dark:hover:bg-theme-secondary-800',
        ])
    >
        @if (strlen($slot) > 0)
            <span>{{ $slot }}</span>
        @else
            <span>
                @if ($isProduction)
                    @lang('general.navbar.live')
                @else
                    @lang('general.navbar.test')
                @endif
            </span>

            <span
                class="transition-default"
                :class="{ 'rotate-180': isOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-2.5 h-2.5"
                />
            </span>
        @endif
    </button>

    <div
        x-show="isOpen"
        @click.away="isOpen = false"
        x-transition
        x-cloak
        class="absolute flex flex-col right-0 top-full md:right-0 py-3 rounded-xl overflow-hidden bg-white dark:bg-theme-secondary-800 whitespace-nowrap"
    >
        <a
            @click="isOpen = false"
            href="{{ Network::mainnetExplorerUrl() }}"
            @class([
                $itemClass,
                $activeClass => $isProduction,
                $inactiveClass => ! $isProduction,
            ])
        >
            <span class="md:hidden">@lang('general.navbar.live')</span>
            <span class="hidden md:block">@lang('general.navbar.live_network')</span>
        </a>

        <a
            @click="isOpen = false"
            href="{{ Network::testnetExplorerUrl() }}"
            @class([
                $itemClass,
                $activeClass => ! $isProduction,
                $inactiveClass => $isProduction,
            ])
        >
            <span class="md:hidden">@lang('general.navbar.test')</span>
            <span class="hidden md:block">@lang('general.navbar.test_network')</span>
        </a>
    </div>
</div>
