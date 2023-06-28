@props([
    'id',
    'label',
    'items',
    'dropdownWidth' => 'w-full',
    'placeholder'   => null,
    'multiple'      => false,
    'itemLangProperties' => [],
])

@php
    $translationKey = null;
    if (is_string($items)) {
        $translationKey = $items;
        $items = trans($items);
    }
@endphp

<div
    x-data="{}"
    x-ref="input-select"
    {{ $attributes->class('space-y-3') }}
>
    <label
        class="font-semibold text-theme-secondary-900 text-lg"
        @click="function (e) {
            e.preventDefault();

            $nextTick(function () {
                $refs['input-select'].querySelector('button').click();
            });
        }"
    >
        {{ $label }}
    </label>

    <x-general.dropdown.dropdown
        dropdown-wrapper-class="w-full flex flex-col relative"
        :width="$dropdownWidth"
        scroll-class="space-y-2"
    >
        <x-slot
            name="button"
            class="border border-theme-secondary-400 dark:border-theme-secondary-700 rounded px-4 py-3.5 w-full leading-[17px] h-11 flex justify-between"
        >
            <span
                x-text="$refs[{{ $id }}]?.innerText ?? '{{ $placeholder }}'"
                :class="{
                    'text-theme-secondary-900': (! Array.isArray({{ $id }}) && {{ $id }} !== null) || (Array.isArray({{ $id }}) && {{ $id }}.length > 0),
                }"
            ></span>

            <span
                class="transition-default"
                :class="{ 'rotate-180': dropdownOpen }"
            >
                <x-ark-icon
                    name="arrows.chevron-down-small"
                    size="w-3 h-3"
                    class="text-theme-secondary-700 dark:text-theme-secondary-200"
                />
            </span>
        </x-slot>

        <x-slot name="buttonExtra">
            {{ $slot }}
        </x-slot>

        @unless ($multiple)
            @foreach ($items as $key => $text)
                <x-general.dropdown.alpine-list-item
                    :id="$key"
                    :variable-name="$id"
                >
                    @if ($translationKey)
                        @lang($translationKey.'.'.$key, $itemLangProperties)
                    @else
                        {{ $text }}
                    @endif
                </x-general.dropdown.alpine-list-item>
            @endforeach
        @else
            @foreach ($items as $key => $text)
                <x-general.dropdown.alpine-list-checkbox
                    :id="$key"
                    :variable-name="$id"
                >
                    @if ($translationKey)
                        @lang($translationKey.'.'.$key, $itemLangProperties)
                    @else
                        {{ $text }}
                    @endif
                </x-general.dropdown.alpine-list-checkbox>
            @endforeach
        @endunless
    </x-general.dropdown.dropdown>
</div>
