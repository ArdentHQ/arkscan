@props(['wallet'])

<div>
    @unless($wallet->isResigned())
        <span>#{{ $wallet->rank() }}</span>
        <span>/</span>
    @endunless

    @if($wallet->isResigned())
        <span class="text-theme-danger-700">
            @lang('pages.delegates.resigned')
        </span>
    @elseif($wallet->rank() > Network::delegateCount())
        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('pages.delegates.standby')
        </span>
    @else
        <span class="text-theme-success-700">
            @lang('pages.delegates.active')
        </span>
    @endif
</div>
