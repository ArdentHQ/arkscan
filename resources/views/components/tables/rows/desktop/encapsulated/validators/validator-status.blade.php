@props(['model'])

<x-general.badge :attributes="$attributes->class('encapsulated-badge')">
    @if ($model->isActive())
        @lang('general.validators.forging-status.active')
    @elseif ($model->isResigned())
        @lang('general.validators.forging-status.resigned')
    @else
        @lang('general.validators.forging-status.standby')
    @endif
</x-general.badge>
