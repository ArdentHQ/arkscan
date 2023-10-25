@props(['model'])

<div class="text-sm leading-4.25">
    @if ($model->hasForged())
        <a
            href="{{ route('block', $model->lastBlock()['id']) }}"
            class="link font-semibold"
        >
            <x-number>{{ $model->lastBlock()['height'] }}</x-number>
        </a>
    @else
        <span class="text-theme-secondary-500">
            @lang('tables.delegate-monitor.tbd')
        </span>
    @endif
</div>
