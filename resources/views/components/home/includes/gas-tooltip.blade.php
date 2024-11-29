@props(['gasTracker'])

<div class="font-semibold min-w-48 space-y-2 p-1 text-xs">
    @foreach ($gasTracker as $title => $fee)
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-1">
                <div class="text-theme-secondary-200">@lang('pages.statistics.gas-tracker.'.$title):</div>
                <div class="text-theme-secondary-700">
                    {{ trans_choice('general.seconds_duration', $fee['duration'], ['duration' => $fee['duration']]) }}
                </div>
            </div>

            <span>
                {{ ExplorerNumberFormatter::networkCurrency($fee['amount'], 2) }} @lang('general.gwei')
            </span>
        </div>
    @endforeach
</div>
