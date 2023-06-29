@props([
    'id',
    'label',
    'items',
    'dropdownWidth' => 'w-full',
    'placeholder'   => null,
    'multiple'      => false,
    'itemLangProperties' => [],
    'selectedPluralizedLangs' => null,
])

@php
    $translationKey = null;
    if (is_string($items)) {
        $translationKey = $items;
        $items = trans($items);
    }
@endphp

<div
    x-data="{
        @if ($multiple)
            get selectedItemCount() {
                return Object.entries({{ $id }}).map((enabled) => enabled).length;
            }
        @endif
    }"
    x-ref="input-select"
    {{ $attributes->class('space-y-3 group') }}
>
    <label
        class="font-semibold text-theme-secondary-900 text-lg group-hover:text-theme-primary-600 transition-default"
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
        dropdown-wrapper-class="flex relative flex-col w-full"
        :width="$dropdownWidth"
        scroll-class="space-y-2"
        :close-on-click="false"
    >
        <x-slot
            name="button"
            class="flex justify-between py-3.5 px-4 w-full h-11 rounded border border-theme-secondary-400 leading-[17px] dark:border-theme-secondary-700"
        >
            @if ($multiple)
                <span x-show="selectedItemCount === 0">
                    {{ $placeholder }}
                </span>

                <div
                    x-show="selectedItemCount > 0"
                    class="text-theme-secondary-900"
                >
                    <span x-text="'(' + selectedItemCount + ')'"></span>

                    <span x-show="selectedItemCount === 1">
                        {{ $selectedPluralizedLangs['singular'] }}
                    </span>

                    <span x-show="selectedItemCount > 1">
                        {{ $selectedPluralizedLangs['plural'] }}
                    </span>
                </div>
            @else
                <span
                    x-text="$refs[{{ $id }}]?.innerText ?? '{{ $placeholder }}'"
                    :class="{
                        'text-theme-secondary-900': (! Array.isArray({{ $id }}) && {{ $id }} !== null) || (Array.isArray({{ $id }}) && {{ $id }}.length > 0),
                    }"
                ></span>
            @endif

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
