<div wire:poll.{{ Network::blockTime() }}s>
    <div class="flex flex-col space-y-3 divide-y sm:flex-row sm:space-y-0 sm:space-x-6 sm:divide-y-0 divide-theme-secondary-300 dark:divide-theme-dark-700 px-4 sm:px-6">
        <div class="flex flex-col flex-1 space-y-3 divide-y divide-theme-secondary-300 dark:divide-theme-dark-700">
            <x-home.stat
                :title="trans('pages.home.statistics.market_cap')"
                :disabled="! Network::canBeExchanged() || $marketCap === null"
            >
                {{ $marketCap }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </x-home.stat>

            <x-home.stat :title="trans('pages.home.statistics.current_supply')" class="pt-3">
                <x-currency :currency="Network::currency()">{{ $supply }}</x-currency>
            </x-home.stat>
        </div>

        <div class="flex flex-col flex-1 pt-3 space-y-3 divide-y sm:pt-0 divide-theme-secondary-300 dark:divide-theme-dark-700">
            <x-home.stat
                :title="trans('pages.home.statistics.volume')"
                :disabled="! Network::canBeExchanged() || $volume === null"
            >
                {{ $volume }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </x-home.stat>

            <x-home.stat
                :title="trans('pages.home.statistics.block_height')"
                class="pt-3"
            >
                <x-number>{{ $height }}</x-number>
            </x-home.stat>
        </div>
    </div>

    <div
        x-data="{
            isDragging: false,
            mouseDownStart: 0,
            startStyle: {
                opacity: 0,
            },
            endStyle: {
                opacity: 1,
            },
            startDragging: function(event) {
                this.isDragging = true;
                mouseDownStart = event.clientX;
                if (event.type === 'touchstart') {
                    mouseDownStart = event.touches[0].clientX;
                }
            },
            stopDragging: function(event) {
                this.isDragging = false;
                mouseDownStart = 0;
            },
            scroll: function(event) {
                if (! this.isDragging) return;

                let moveX = event.clientX - mouseDownStart;
                if (event.type === 'touchmove') {
                    moveX = event.touches[0].clientX - mouseDownStart;
                }

                this.$el.scroll({
                    left: this.$el.scrollLeft - moveX,
                });

                this.adjustFade();

                mouseDownStart = event.clientX;
                if (event.type === 'touchmove') {
                    mouseDownStart = event.touches[0].clientX;
                }

                event.preventDefault();
            },

            adjustFade: function() {
                const scroller = this.$refs['gas-ticker'];

                this.startStyle.opacity = scroller.scrollLeft / (scroller.scrollWidth - scroller.clientWidth);
                this.endStyle.opacity = 1 - (scroller.scrollLeft / (scroller.scrollWidth - scroller.clientWidth));
            }
        }"

        @resize.window="adjustFade"

        class="relative"
    >
        <div
            x-ref="fade-start"
            wire:key="gas-ticker:fade-start"
            class="from-[#F7FAFB00] to-theme-secondary-100 dark:from-[#191d2200] dark:to-theme-dark-950 bg-gradient-to-l absolute left-0 h-full w-12 top-0 rounded-bl-xl hidden lg:block xl:hidden pointer-events-none opacity-0"
            x-bind:style="startStyle"
        ></div>

        <div
            class="flex flex-col py-3 px-4 mt-4 -mb-3 space-y-2 font-semibold rounded-b-xl sm:flex-row sm:items-center sm:px-6 sm:space-y-0 sm:space-x-3 md:-mb-6 bg-theme-secondary-100 dark:bg-theme-dark-950 dark:text-theme-dark-200 overflow-hidden select-none"
            x-on:mousedown="startDragging"
            x-on:touchstart="startDragging"
            x-on:mouseup="stopDragging"
            x-on:touchend="stopDragging"
            x-on:mousemove="scroll($event)"
            x-on:touchmove="scroll($event)"
            x-ref="gas-ticker"
        >
            <div class="text-sm whitespace-nowrap">
                @lang('pages.statistics.gas-tracker.gas_tracker')
            </div>

            <div class="flex items-center sm:space-x-2">
                @foreach ($gasTracker as $title => $value)
                    <x-home.includes.gas-badge
                        :title="trans('pages.statistics.gas-tracker.'.$title)"
                        :class="Arr::toCssClasses(['hidden sm:block' => $title !== 'average'])"
                    >
                        <x-slot name="value">
                            @if (Network::canBeExchanged())
                                <span>{{ ExchangeRate::convert($value) }}</span>
                                <span>{{ Settings::currency() }}</span>
                            @else
                                <span>{{ $value }}</span>
                                <span>@lang('general.gwei')</span>
                            @endif
                        </x-slot>
                    </x-home.includes.gas-badge>
                @endforeach
            </div>
        </div>

        <div
            x-ref="fade-end"
            wire:key="gas-ticker:fade-end"
            class="from-[#F7FAFB00] to-theme-secondary-100 dark:from-[#191d2200] dark:to-theme-dark-950 bg-gradient-to-r absolute right-0 h-full w-12 top-0 rounded-br-xl hidden lg:block xl:hidden pointer-events-none"
            x-bind:style="endStyle"
        ></div>
    </div>
</div>
