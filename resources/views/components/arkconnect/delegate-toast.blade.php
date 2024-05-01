@props([
    'text',
    'linkText',
    'dividerClass',
    'onClose',
    'type',
])

<div
    {{ $attributes }}
    x-data="{ preventPopup: false }"
    class="flex fixed bottom-2 left-2 right-2 sm:bottom-4 sm:left-auto sm:right-6 z-20 sm:max-w-[410px] arkconnect-toast"
    x-cloak
>
    <x-ark-toast
        class="mx-auto !max-w-7xl"
        :type="$type"
        alpine-close="if (preventPopup) {
            {{ $onClose }}
        }"
        hide-spinner
        always-show-title
    >
        {!! $text !!}

        <a
            class="link font-semibold mt-2"
            href="{{ route('delegates') }}"
        >
            {!! $linkText !!}
        </a>

        <div @class([
            'border-t border-dashed my-3',
            $dividerClass,
        ])></div>

        <x-ark-checkbox
            name="do_not_remind"
            :label="trans('forms.general.do_not_remind')"
            label-classes="text-black dark:text-theme-dark-50"
            class=""
            x-model="preventPopup"
        />
    </x-ark-toast>
</div>
