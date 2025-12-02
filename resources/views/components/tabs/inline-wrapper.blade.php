@props([
    'xData' => '{}',
    'onSelected' => null,
    'defaultSelected' => '',
])

<div
    x-data="{
        showOverflowIndicators: false,
        checkOverflow: function () {
            const container = this.$refs.container;

            this.showOverflowIndicators = container.scrollWidth > container.clientWidth;
        },
    }"
    x-init="checkOverflow();"
    class="relative -mx-6 sm:mx-0"
    x-resize="checkOverflow()"
>
    <div x-show="showOverflowIndicators" x-cloak>
        <div class="absolute top-0 left-0 z-20 w-12 h-12 h-full bg-gradient-to-r pointer-events-none from-theme-secondary-200 to-theme-secondary-200/0 dark:from-theme-dark-950 dark:to-theme-dark-950/0"></div>
        <div class="absolute top-0 right-0 z-20 w-12 h-12 h-full bg-gradient-to-l pointer-events-none from-theme-secondary-200 to-theme-secondary-200/0 dark:from-theme-dark-950 dark:to-theme-dark-950/0"></div>
    </div>

    <div
        x-ref="container"
        class="px-6 sm:px-0 py-2 sm:py-0 mb-4 sm:mb-0 bg-theme-secondary-200 dark:bg-theme-dark-950 sm:!bg-transparent w-screen sm:w-auto overflow-scroll no-scrollbar"
    >
        <div
            {{ $attributes->class([
                'items-center justify-between inline-flex bg-theme-secondary-200 rounded-xl dark:bg-theme-dark-950 relative z-10 sm:p-1',
            ])}}
            x-data="Tabs(
                '{{ $defaultSelected }}',
                {{ $xData }}
                @if($onSelected)
                , {{ $onSelected }}
                @endif
            )"
        >
            <div
                role="tablist"
                class="flex !px-0 space-x-1 pr-6 sm:pr-0"
            >
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
