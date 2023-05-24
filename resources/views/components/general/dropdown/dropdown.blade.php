@props([
    'button',
    'content',
    'dropdownWrapperClass' => null,
    'dropdownClass' => null,
    'disabled' => false,
    'disabledButtonClass' => 'text-theme-secondary-500 dark:text-theme-secondary-700 bg-theme-secondary-200 dark:bg-theme-secondary-800',
    'activeButtonClass' => 'bg-theme-secondary-200 dark:bg-theme-secondary-800 md:bg-white md:dark:text-theme-secondary-600 md:hover:text-theme-secondary-900 md:hover:bg-theme-secondary-200 md:dark:bg-theme-secondary-900 dark:hover:bg-theme-secondary-800 text-theme-secondary-700 dark:text-theme-secondary-200',
])

<div
    {{ $attributes->class('relative') }}
    x-data="{ dropdownOpen: false }"
>
    <x-ark-dropdown
        :wrapper-class="Arr::toCssClasses(['inline-block', $dropdownWrapperClass])"
        :init-alpine="false"
        with-placement="bottom"
        :dropdown-classes="Arr::toCssClasses(['min-w-40 transition-opacity', $dropdownClass])"
        dropdown-content-classes="bg-white dark:bg-theme-secondary-800 rounded-xl shadow-lg py-2"
        :disabled="$disabled"
        z-index="z-20"
        button-class="w-full"
    >
        <x-slot name="button">
            <div {{ $button->attributes->class([
                'inline-flex items-center transition-default',
                $disabledButtonClass => $disabled,
                $activeButtonClass => ! $disabled,
            ]) }}>
                {{ $button }}
            </div>
        </x-slot>

        <div class="flex overflow-y-auto flex-col h-full custom-scroll">
            {{ $content }}
        </div>
    </x-ark-dropdown>
</div>
