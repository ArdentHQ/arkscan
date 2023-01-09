@props ([
    'value'
])

<div
    {{ $attributes }}
    x-data="clipboard()"
    x-init="initClipboard()"
>
    <button
        class="clipboard"
        type="button"
        x-on:click="copy('{{ $value }}')"
        tooltip-content="@lang ('tooltips.copied')"
    >
        <x-ark-icon
            name="copy"
            size="sm"
            class="text-theme-primary-400"
        />
    </button>
</div>
