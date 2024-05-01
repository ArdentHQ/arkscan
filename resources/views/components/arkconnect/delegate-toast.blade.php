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
    class="flex fixed right-2 bottom-2 left-2 z-20 sm:bottom-4 sm:right-6 sm:left-auto arkconnect-toast sm:max-w-[410px]"
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
            class="mt-2 font-semibold link"
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
