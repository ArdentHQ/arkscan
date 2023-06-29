@props([
    'button',
    'buttonClass' => "w-full focus-visible:ring-2 focus-visible:ring-theme-primary-500 focus-visible:dark:ring-theme-primary-300 focus-visible:ring-inset",
    'buttonWrapperClass' => null,
    'dropdownWrapperClass' => null,
    'dropdownClass' => null,
    'dropdownPadding' => 'py-2',
    'dropdownBackground' => 'bg-white dark:bg-theme-secondary-800',
    'width' => 'min-w-40',
    'scrollClass' => null,
    'disabled' => false,
    'initApine' => true,
    'closeOnClick' => true,
    'onClose' => null,
    'buttonExtra' => null,
    'placement' => 'bottom',
    'placementFallbacks' => null,
    'disabledButtonClass' => 'text-theme-secondary-500 dark:text-theme-secondary-700 bg-theme-secondary-200 dark:bg-theme-secondary-800',
    'activeButtonClass' => 'bg-theme-secondary-200 dark:bg-theme-secondary-800 md:bg-white md:dark:text-theme-secondary-600 md:hover:text-theme-secondary-900 md:hover:bg-theme-secondary-200 md:dark:bg-theme-secondary-900 dark:hover:bg-theme-secondary-800 text-theme-secondary-700 dark:text-theme-secondary-200',
])

<div
    {{ $attributes->class('relative') }}
    @if ($initApine)
        x-data="{ dropdownOpen: false }"
    @endif
>
    <x-ark-dropdown
        :wrapper-class="Arr::toCssClasses(['inline-block', $dropdownWrapperClass])"
        :init-alpine="false"
        :with-placement="$placement"
        :dropdown-classes="Arr::toCssClasses(['transition-opacity', $width, $dropdownClass])"
        :dropdown-content-classes="Arr::toCssClasses(['rounded-xl shadow-lg', $dropdownBackground, $dropdownPadding])"
        :disabled="$disabled"
        z-index="z-20"
        :button-class="$buttonClass"
        :close-on-click="$closeOnClick"
        :on-close="$onClose"
        :placement-fallbacks="$placementFallbacks"
    >
        <x-slot name="button">
            <div @class([
                'w-full',
                'flex flex-col space-y-3' => $buttonExtra !== null,
                $buttonWrapperClass,
            ])>
                <div {{ $button->attributes->class([
                    'inline-flex items-center transition-default',
                    $disabledButtonClass => $disabled,
                    $activeButtonClass => ! $disabled,
                ]) }}>
                    {{ $button }}
                </div>

                {{ $buttonExtra }}
            </div>
        </x-slot>

        <div @class(["flex overflow-y-auto flex-col h-full custom-scroll overscroll-contain", $scrollClass])>
            {{ $slot }}
        </div>
    </x-ark-dropdown>
</div>
