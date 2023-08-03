@props(['model'])

{{-- TODO: use <x-general.badge when PR #465 is merged --}}
<div {{ $attributes->class('text-xs font-semibold rounded border border-transparent dark:bg-transparent px-[3px] py-[2px] bg-theme-secondary-200 leading-[15px] dark:border-theme-secondary-800 dark:text-theme-secondary-500') }}>
    @if ($model->isActive())
        @lang('general.delegates.forging-status.active')
    @elseif ($model->isStandby())
        @lang('general.delegates.forging-status.standby')
    @else
        @lang('general.delegates.forging-status.resigned')
    @endif
</div>
