@props([
    'button',
    'buttonClass' => "w-full focus-visible:ring-2 focus-visible:ring-theme-primary-500 focus-visible:dark:ring-theme-dark-blue-300 focus-visible:ring-inset",
    'buttonWrapperClass' => 'w-full',
    'dropdownWrapperClass' => null,
    'dropdownClass' => null,
    'dropdownPadding' => 'py-2',
    'dropdownBackground' => 'bg-white dark:bg-theme-dark-700',
    'width' => 'min-w-40',
    'scrollClass' => null,
    'disabled' => false,
    'initApine' => true,
    'closeOnClick' => true,
    'onClose' => null,
    'buttonExtra' => null,
    'placement' => 'bottom',
    'placementFallbacks' => null,
    'dropdownRounding' => 'rounded-xl',
    'disabledButtonClass' => 'text-theme-secondary-500 dark:text-theme-dark-500 bg-theme-secondary-200 dark:bg-theme-dark-700',
    'activeButtonClass' => 'bg-theme-secondary-200 dark:bg-theme-dark-800 md:bg-white md:dark:text-theme-dark-600 md:hover:text-theme-secondary-900 md:hover:bg-theme-secondary-200 md:dark:bg-theme-dark-900 dark:hover:bg-theme-secondary-800 text-theme-secondary-700 dark:text-theme-dark-200',
    'dropdownProperty' => 'dropdownOpen',
    'contentClass' => null,
])

<div
    {{ $attributes->class('relative') }}
    @if ($initApine)
        x-data="{ {{ $dropdownProperty }}: false }"
    @endif
>
    <x-ark-dropdown
        :wrapper-class="Arr::toCssClasses(['inline-block', $dropdownWrapperClass])"
        :init-alpine="false"
        :with-placement="$placement"
        :dropdown-classes="Arr::toCssClasses(['transition-opacity', $width, $dropdownClass])"
        :dropdown-content-classes="Arr::toCssClasses(['shadow-lg', $dropdownRounding, $dropdownBackground, $dropdownPadding])"
        :disabled="$disabled"
        z-index="z-20"
        :button-class="$buttonClass"
        :close-on-click="$closeOnClick"
        :on-close="$onClose"
        :placement-fallbacks="$placementFallbacks"
        :dropdown-property="$dropdownProperty"
        :content-class="$contentClass"
    >
        <x-slot name="button">
            <div @class([
                'flex flex-col space-y-3' => $buttonExtra !== null,
                $buttonWrapperClass,
            ])>
                <div {{ $button->attributes->class([
                    'flex items-center transition-default',
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
