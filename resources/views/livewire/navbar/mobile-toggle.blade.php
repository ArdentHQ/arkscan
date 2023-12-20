@php($isDisabled = app()->isDownForMaintenance())

<div
    @class([
        'relative h-8 rounded bg-theme-secondary-200',
        'dark:bg-theme-dark-800 text-theme-secondary-500 dark:text-theme-dark-700' => $isDisabled,
        'dark:bg-theme-dark-900' => ! $isDisabled,
    ])"
>
    <div class="flex items-center">
    @foreach ($options as $option)
        <div class="relative p-2 pr-1 last:pr-2">
            @if ($currentValue === $option['value'])
                <div @class([
                    'absolute top-1 rounded w-6 h-6 transition-default dark:bg-theme-dark-700 left-1',
                    'bg-theme-secondary-200 ' => $isDisabled,
                    'bg-white' => ! $isDisabled,
                ])></div>
            @endif

            <button
                wire:click="setValue('{{ $option['value'] }}')"
                @class([
                    'relative z-10',
                    'dark:text-theme-dark-300' => ! $isDisabled && $currentValue === $option['value'],
                    'text-theme-secondary-900 dark:text-theme-dark-300' => ! $isDisabled && $currentValue !== $option['value'],
                    'text-theme-secondary-500 dark:text-theme-dark-700' => $isDisabled,
                ])
                @if ($isDisabled)
                    disabled
                @endif
            >
                <x-ark-icon
                    :name="$option['icon']"
                    size="sm"
                />
            </button>
        </div>
    @endforeach
    </div>
</div>
