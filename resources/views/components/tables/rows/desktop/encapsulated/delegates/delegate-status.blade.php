@props(['model'])

<x-general.badge>
    @if ($model->isActive())
        @lang('general.delegates.forging-status.active')
    @elseif ($model->isStandby())
        @lang('general.delegates.forging-status.standby')
    @else
        @lang('general.delegates.forging-status.resigned')
    @endif
</x-general.badge>
