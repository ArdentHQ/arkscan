@props([
    'icon',
    'label',
    'disabled' => false,
])

<div {{ $attributes->merge(['class' => 'flex py-4 bg-white dark:bg-theme-secondary-900 rounded-xl px-6']) }}>
    <div class="flex flex-grow items-center space-x-4">
        <span
            class="flex items-center justify-center w-11 h-11 border-2 rounded-full @if($disabled) dark:border-theme-secondary-600 border-theme-secondary-500 dark:text-theme-secondary-600 text-theme-secondary-500 @else border-theme-secondary-900 text-theme-secondary-900 dark:text-theme-secondary-700 dark:border-theme-secondary-700 @endif"
        >
            <x-ark-icon :name="$icon" />
        </span>
        <span class="flex flex-col flex-grow justify-between h-full">
            <span class="text-sm font-semibold leading-none whitespace-nowrap text-theme-secondary-500 dark:text-theme-secondary-600">{{$label}}</span>

            @if ($disabled)
                <span class="font-semibold leading-none whitespace-nowrap text-theme-secondary-500 dark:text-theme-secondary-600">
                    @lang('general.not_available')
                </span>
            @else
                <span class="font-semibold leading-none whitespace-nowrap dark:text-white text-theme-secondary-900">
                    {{ $slot }}
                </span>
            @endif
        </span>
    </div>

    @isset($side)
        {{ $side }}
    @endisset
</div>
