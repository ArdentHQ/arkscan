@props([
    'name',
    'label'   => null,
    'minDate' => 0,
    'format'  => 'DD/MM/YYYY',
    'maxDate' => 'new Date()',
])

<div {{ $attributes->class('flex flex-col space-y-2 flex-1') }}>
    @if ($label)
        <label class="dark:text-theme-dark-200">
            {{ $label }}
        </label>
    @endif

    <div class="flex flex-1 items-center pr-4 space-x-2 justify-between bg-white dark:bg-theme-dark-900 rounded border border-theme-secondary-400 dark:border-theme-dark-500">
        <div class="grid grid-cols-1">
            <input
                x-data
                x-ref="{{ $name }}"
                x-init="new Pikaday({
                    field: $refs['{{ $name }}'],
                    format: '{{ $format }}',
                    minDate: {{ $minDate }},
                    maxDate: {{ $maxDate }},
                    onSelect(date) {
                        {{ $xModel }} = date;
                    },
                    toString(date, format) {
                        return date.toLocaleDateString(navigator.language, { year: 'numeric', month: '2-digit', day: '2-digit' });
                    },
                })"
                type="text"
                onchange="this.dispatchEvent(new InputEvent('input'))"
                width="100%"
                class="pl-4 py-3 rounded dark:bg-theme-dark-900 dark:text-theme-dark-50 placeholder:dark:text-theme-dark-200"
                placeholder="{{ $format }}"
            />
        </div>

        <div>
            <x-ark-icon
                name="calendar-without-dots"
                class="text-theme-primary-600 dark:text-theme-dark-300"
            />
        </div>
    </div>
</div>
