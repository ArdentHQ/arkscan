@props(['deferLoading' => true])

<div
    x-show="tab === 'recent-votes'"
    id="recent-votes-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:validators.recent-votes :defer-loading="$deferLoading" />
</div>
