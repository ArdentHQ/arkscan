<div wire:poll.{{ Network::blockTime() }}s>
    <div class="flex flex-col px-4 space-y-3 divide-y sm:flex-row sm:px-6 sm:space-y-0 sm:space-x-6 sm:divide-y-0 divide-theme-secondary-300 dark:divide-theme-dark-700">
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
                <x-currency :currency="Network::currency()">{{ round($supply) }}</x-currency>
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
        <div class="flex flex-col pl-4 mt-4 -mb-3 space-y-2 font-semibold rounded-b-xl sm:flex-row sm:items-center sm:pl-6 sm:space-y-0 sm:space-x-3 md:-mb-6 bg-theme-secondary-100 dark:bg-theme-dark-950 dark:text-theme-dark-200">
            <div class="pt-3 text-sm whitespace-nowrap sm:py-3">
                @lang('pages.statistics.gas-tracker.gas_tracker')
            </div>

            <div class="overflow-hidden relative flex-1">
                <div
                    x-ref="fade-start"
                    wire:key="gas-ticker:fade-start"
                    class="hidden absolute top-0 left-0 w-12 h-full bg-gradient-to-l rounded-bl-xl opacity-0 pointer-events-none lg:block xl:hidden from-[#F7FAFB00] to-theme-secondary-100 dim:from-[#10162700] z-5 dark:from-[#191d2200] dark:to-theme-dark-950"
                    x-bind:style="startStyle"
                ></div>

                <div
                    class="flex overflow-hidden relative items-center pr-4 pb-3 select-none sm:py-3 sm:space-x-2"
                    x-on:mousedown="startDragging"
                    x-on:touchstart="startDragging"
                    x-on:mouseup="stopDragging"
                    x-on:touchend="stopDragging"
                    x-on:mousemove="scroll($event)"
                    x-on:touchmove="scroll($event)"
                    x-ref="gas-ticker"
                >
                    @foreach ($gasTracker as $title => $fee)
                        <x-home.includes.gas-badge
                            :title="trans('pages.statistics.gas-tracker.'.$title)"
                            :class="Arr::toCssClasses(['hidden sm:block' => $title !== 'average'])"
                        >
                            <x-slot name="value">
                                @if (Network::canBeExchanged())
                                    <span>{{ ExchangeRate::convert($fee['amount']) }}</span>
                                    <span>{{ Settings::currency() }}</span>
                                @else
                                    <span>{{ round($fee['amount']->__toString()) }}</span>
                                    <span>@lang('general.gwei')</span>
                                @endif
                            </x-slot>
                        </x-home.includes.gas-badge>
                    @endforeach

                    <div class="ml-2 sm:hidden">
                        <x-ark-info
                            type="info"
                            :html-tooltip="view('components.home.includes.gas-tooltip', ['gasTracker' => $gasTracker])->render()"
                        />
                    </div>
                </div>

                <div
                    x-ref="fade-end"
                    wire:key="gas-ticker:fade-end"
                    class="hidden absolute top-0 right-0 w-12 h-full bg-gradient-to-r rounded-br-xl pointer-events-none lg:block xl:hidden from-[#F7FAFB00] to-theme-secondary-100 dim:from-[#10162700] z-5 dark:from-[#191d2200] dark:to-theme-dark-950"
                    x-bind:style="endStyle"
                ></div>
            </div>
        </div>
    </div>
</div>
