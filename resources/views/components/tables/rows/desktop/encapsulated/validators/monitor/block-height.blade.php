@props(['model'])

<div class="text-sm font-semibold leading-4.25">
    @if ($model->hasForged())
        <a
            href="{{ route('block', $model->lastBlock()['hash']) }}"
            class="link"
        >
            <x-number>{{ $model->lastBlock()['number'] }}</x-number>
        </a>
    @else
        <span class="text-theme-secondary-500 dark:text-theme-dark-500">
            @if ($model->justMissed())
                @lang('general.na')
            @else
                @lang('tables.validator-monitor.tbd')
            @endif
        </span>
    @endif
</div>
