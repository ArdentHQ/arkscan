@props([
    'title',
    'value',
    'tooltip' => null,
    'hasEmptyValue' => false,
])

<div class="flex justify-between items-center text-sm font-semibold md:text-base">
    <div class="dark:text-theme-secondary-500">{{ $title }}</div>

    @if ($hasEmptyValue || $value === null || strlen($value) === 0)
        <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
            @lang('general.na')
        </div>
    @else
        <div
            class="text-theme-secondary-900 dark:text-theme-secondary-200"
            @if ($tooltip)
                data-tippy-content="{{ $tooltip }}"
            @endif
        >
            {{ $value }}
        </div>
    @endif
</div>
