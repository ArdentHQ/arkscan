<div
    x-show="tab === 'missed-blocks'"
    id="missed-blocks-list"
    {{ $attributes->class('w-full') }}
>
    <livewire:delegates.missed-blocks />
</div>
