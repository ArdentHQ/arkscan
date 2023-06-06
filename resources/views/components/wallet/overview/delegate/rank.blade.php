@props(['wallet'])

<div>
    <span>#{{ $wallet->rank() }}</span>
    <span>/</span>

    @if($wallet->isResigned())
        <span class="text-theme-danger-400">
            @lang('pages.delegates.resigned')
        </span>
    @elseif($wallet->rank() > Network::delegateCount())
        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('pages.delegates.standby')
        </span>
    @else
        <span class="text-theme-success-600">
            @lang('pages.delegates.active')
        </span>
    @endif
</div>
