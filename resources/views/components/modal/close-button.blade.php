@props(['click'])

{{-- Used by foundation's external-link-confirm modal, which references this component without shipping it in 20.x --}}
<button
    type="button"
    @click="{{ $click }}"
    {{ $attributes->class('button button-secondary transition-default h-11 w-11 rounded-none p-0 text-theme-secondary-900 sm:mr-6 sm:mt-6 sm:rounded') }}
>
    <x-ark-icon name="cross" size="sm" class="m-auto" />
</button>
