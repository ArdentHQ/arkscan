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
    x-ref="selectField{{ Str::studly($id) }}"
    @unless ($multiple)
        x-data="{
            dropdownOpen: false,
        }"
    @else
        x-data="{
            dropdownOpen: false,
            updateSelectedCount() {
                const enabledCount = Object.values({{ $id }}).filter(enabled => enabled).length;

                $store['selectField{{ Str::studly($id) }}'].selectedItems.count = enabledCount;
                $store['selectField{{ Str::studly($id) }}'].selectAll = enabledCount === Object.values({{ $id }}).length;

            },
        }"
        x-init="() => {
            Alpine.store('selectField{{ Str::studly($id) }}', {
                selectAll: false,
                selectedItems: {
                    count: 0,
                },
            });

            $watch('{{ $id }}', updateSelectedCount);

            updateSelectedCount();
        }"
    @endunless
    {{ $attributes->class('group/label') }}
>
    <label
        class="text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-50 transition-default group-hover/label:text-theme-primary-600 group-hover/label:dark:text-theme-dark-blue-500 block pb-3"
        @click="function (e) {
            e.preventDefault();

            $nextTick(function () {
                $refs['selectField{{ Str::studly($id) }}'].querySelector('button').click();
            });
        }"
    >
        {{ $label }}
    </label>

    <x-general.dropdown.dropdown
        dropdown-wrapper-class="flex relative flex-col w-full"
        dropdown-class="dark:bg-theme-secondary-800 rounded"
        :width="$dropdownWidth"
        :close-on-click="! $multiple"
        :init-alpine="false"
    >
        <x-slot
            name="button"
            class="flex justify-between py-3.5 px-4 w-full h-11 rounded border border-theme-secondary-400 leading-[17px] dark:border-theme-dark-500 dark:text-theme-dark-200"
        >
            @if ($multiple)
                <span x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count === 0">
                    {{ $placeholder }}
                </span>

                <div
                    x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count > 0"
                    class="text-theme-secondary-900 dark:text-theme-dark-50"
                >
                    <span x-text="`(${$store['selectField{{ Str::studly($id) }}'].selectedItems.count})`"></span>

                    <span x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count === Object.values({{ $id }}).length">
                        @lang('general.all')
                    </span>

                    <span x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count === 1">
                        {{ $selectedPluralizedLangs['singular'] }}
                    </span>

                    <span x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count > 1">
                        {{ $selectedPluralizedLangs['plural'] }}
                    </span>
                </div>
            @else
                <span
                    x-text="$refs[{{ $id }}]?.innerText ?? '{{ $placeholder }}'"
                    :class="{
                        'text-theme-secondary-900 dark:text-theme-dark-50': (! Array.isArray({{ $id }}) && {{ $id }} !== null) || (Array.isArray({{ $id }}) && {{ $id }}.length > 0),
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
                    class="text-theme-secondary-700 dark:text-theme-dark-200"
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
            <x-general.dropdown.alpine-list-checkbox
                id="selectField{{ Str::studly($id) }}.selectAll"
                variable-name="$store"
                x-on:click="(e) => {
                    for (const key of Object.keys({{ $id }})) {
                        {{ $id }}[key] = e.target.checked;
                    }
                }"
            >
                <span>@lang('general.select_all')</span>

                <span>{{ $label }}</span>
            </x-general.dropdown.alpine-list-checkbox>

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

    @if ($multiple)
        <div
            x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count > 0"
            class="flex flex-wrap items-center gap-3 mt-3"
        >
            @foreach ($items as $key => $text)
                <div
                    x-show="{{ $id }}.{{ $key }} === true"
                    class="border border-transparent dark:border-theme-dark-600 inline-flex cursor-pointer items-center space-x-2 bg-theme-primary-100 dark:bg-theme-dark-800 text-theme-primary-600 dark:text-white p-2.5 rounded font-semibold hover:bg-theme-primary-700 hover:dark:bg-theme-primary-700 hover:dark:border-theme-primary-700 hover:text-white transition-default group text-sm"
                    @click="{{ $id }}.{{ $key }} = false"
                >
                    <div>
                        @if ($translationKey)
                            @lang($translationKey.'.'.$key, $itemLangProperties)
                        @else
                            {{ $text }}
                        @endif
                    </div>

                    <button
                        type="button"
                        class="p-1 text-theme-secondary-700 dark:text-theme-dark-200 group-hover:text-white group-hover:dark:text-white"
                    >
                        <x-ark-icon
                            name="cross-small"
                            size="2xs"
                        />
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
