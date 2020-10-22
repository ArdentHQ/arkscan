@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.9.1/dayjs.min.js"></script>
    <script src="{{ mix('js/chart.js')}}"></script>
@endpush

<div
    x-data="makeChart('{{ $identifier }}', '{{ $coloursScheme }}')"
    x-init="renderChart()"
    class="flex flex-col w-full bg-white border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex flex-col w-full">
        <div class="relative flex items-center justify-between w-full">
            <h2 class="text-2xl">@lang("pages.home.charts.{$identifier}")</h2>

            <x-ark-dropdown dropdown-classes="left-0 w-32 mt-3" button-class="w-32 h-10 dropdown-button" :init-alpine="false">
                @slot('button')
                    <div class="flex items-center justify-end w-full space-x-2 font-semibold flex-inline text-theme-secondary-700">
                        <span x-text="period"></span>
                        <span :class="{ 'rotate-180': open }" class="transition duration-150 ease-in-out">
                            @svg('chevron-up', 'h-3 w-3')
                        </span>
                    </div>
                @endslot
                <div class="py-3">
                    @foreach (array_keys(trans('pages.home.charts.periods')) as $period)
                        <div class="cursor-pointer dropdown-entry" :class="{ 'text-theme-danger-400 bg-theme-danger-100': isActivePeriod('{{ ucfirst($period) }}') === true}" @click="setPeriod('{{ $period }}')">
                            @lang("pages.home.charts.periods." . $period)
                        </div>
                    @endforeach
                </div>
            </x-ark-dropdown>
        </div>
        <div class="flex justify-between w-full mt-5 mb-5">
            <div class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500">@lang("pages.home.charts.min_{$identifier}")</span>
                    <span class="font-semibold" x-text="priceMin + ` ${currency}`"></span>
                </div>
            </div>

            <div class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500">@lang("pages.home.charts.max_{$identifier}")</span>
                    <span class="font-semibold" x-text="priceMax + ` ${currency}`">0.02477504 BTC</span>
                </div>
            </div>

            <div class="flex items-center pr-5 mr-5">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500">@lang("pages.home.charts.avg_{$identifier}")</span>
                    <span class="font-semibold" x-text="priceAvg + ` ${currency}`">0.01570092 BTC</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex w-full" style="height: 340px;">
        <canvas id="{{ $identifier ?? 'priceChart' }}"></canvas>
    </div>
</div>
