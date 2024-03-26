@props(['model'])

<x-general.badge :attributes="$attributes->class('encapsulated-badge')">
    @if ($model->isActive())
        @lang('general.validators.forging-status.active')
    @elseif ($model->isStandby())
        @lang('general.validators.forging-status.standby')
    @else
        @lang('general.validators.forging-status.resigned')
    @endif
</x-general.badge>
