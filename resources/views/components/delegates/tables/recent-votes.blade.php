<div
    x-show="tab === 'recent-votes'"
    id="recent-votes-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:delegates.recent-votes />
</div>
