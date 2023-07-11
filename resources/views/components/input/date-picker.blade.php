@props([
    'name',
    'xModel',
    'label'   => null,
    'minDate' => 0,
    'format'  => 'DD/MM/YYYY',
    'maxDate' => 'new Date()',
    'xOnChange' => null,
    'xInit' => null,
])

<div {{ $attributes->class('flex flex-col space-y-2 flex-1') }}>
    @if ($label)
        <label class="dark:text-theme-dark-200">
            {{ $label }}
        </label>
    @endif

    <div
        x-data
        class="flex flex-1 justify-between items-center bg-white rounded border border-theme-secondary-400 dark:bg-theme-dark-900 dark:border-theme-dark-500"
    >
        <div class="grid grid-cols-1">
            <input
                x-ref="{{ $name }}"
                x-init="() => {
                    const datePicker = new Pikaday({
                        field: $refs['{{ $name }}'],
                        format: '{{ $format }}',
                        minDate: {{ $minDate }},
                        maxDate: {{ $maxDate }},
                        onSelect(date) {
                            {{ $xModel }} = date;
                        },
                        onOpen() {
                            $dispatch('pauseFocusTrap');
                        },
                        onClose(date) {
                            if (this.getDate() === null) {
                                this.clear();
                                {{ $xModel }} = null;
                            } else {
                                this.setDate(this.getDate());
                                {{ $xModel }} = this.getDate();

                                @if ($xOnChange)
                                    {{ $xOnChange }}(this.getDate());
                                @endif
                            }

                            $dispatch('resumeFocusTrap');
                        },
                        toString(date, format) {
                            return date.toLocaleDateString(navigator.language, { year: 'numeric', month: '2-digit', day: '2-digit' });
                        },
                    });

                    @if ($xInit)
                        {{ $xInit }}(datePicker);
                    @endif
                }"
                type="text"
                onchange="this.dispatchEvent(new InputEvent('input'))"
                width="100%"
                class="py-3 pl-4 rounded placeholder:dark:text-theme-dark-200 dark:bg-theme-dark-900 dark:text-theme-dark-50"
                placeholder="{{ $format }}"
                x-on:keydown.backspace="(e) => {
                    e.stopImmediatePropagation();

                    return true;
                }"
            />
        </div>

        <div
            class="flex items-center pr-4 pl-2 h-full cursor-pointer"
            x-on:click="$refs['{{ $name }}'].click()"
        >
            <x-ark-icon
                name="calendar-without-dots"
                class="text-theme-primary-600 dark:text-theme-dark-300"
            />
        </div>
    </div>
</div>
