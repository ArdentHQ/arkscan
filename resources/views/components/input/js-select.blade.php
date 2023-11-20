@props([
    'id',
    'label',
    'items',
    'dropdownWidth'           => 'w-full',
    'placeholder'             => null,
    'multiple'                => false,
    'itemLangProperties'      => [],
    'itemCriteria'            => null,
    'selectedPluralizedLangs' => null,
    'extraItems'              => [],
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
    {{ $attributes }}
>
    <label class="block pb-3 text-lg font-semibold text-theme-secondary-900 transition-default dark:text-theme-dark-50">
        {{ $label }}
    </label>

    <x-general.dropdown.dropdown
        dropdown-wrapper-class="flex relative flex-col w-full"
        dropdown-class="rounded"
        dropdown-rounding="rounded"
        dropdown-padding="py-1 mx-6 sm:mx-0"
        :width="$dropdownWidth"
        :close-on-click="! $multiple"
        :init-alpine="false"
        button-class="w-full"
        button-wrapper-class="w-full rounded-md"
        active-button-class="bg-white dark:text-theme-dark-600 dark:bg-theme-dark-900"
    >
        <x-slot
            name="button"
            class="flex justify-between py-3.5 px-4 w-full h-11 rounded border border-theme-secondary-400 leading-4.25 outline outline-1 outline-transparent dark:border-theme-dark-500 dark:text-theme-dark-200 hover:border-theme-primary-400 hover:dark:border-theme-dark-blue-600 hover:outline-theme-primary-400 hover:dark:outline-theme-dark-blue-600"
        >
            @if ($multiple)
                <span
                    x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count === 0"
                    class="dark:text-theme-dark-200"
                >
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
                    x-html="$refs[{{ $id }}]?.innerHTML ?? '{{ $placeholder }}'"
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
            @foreach ($items as $key => $item)
                <x-general.dropdown.alpine-list-item
                    :id="$key"
                    :variable-name="$id"
                >
                    <x-input.includes.item-text
                        :id="$id"
                        :item="$item"
                        :key="$key"
                        :translation-key="$translationKey"
                        :item-lang-properties="$itemLangProperties"
                    />
                </x-general.dropdown.alpine-list-item>
            @endforeach

            @if (count($extraItems) > 0)
                <div class="flex flex-col border-t border-theme-secondary-300 dark:border-theme-dark-500">
                    @foreach ($extraItems as $item)
                        <x-general.dropdown.alpine-list-item
                            :id="$item['value']"
                            :variable-name="$id"
                        >
                            <x-input.includes.item-text
                                :id="$id"
                                :item="$item['text']"
                                :key="$item['value']"
                            />
                        </x-general.dropdown.alpine-list-item>
                    @endforeach
                </div>
            @endif
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

            @foreach ($items as $key => $item)
                @if ($itemCriteria !== null && $itemCriteria($key) === false)
                    @continue
                @endif

                <x-general.dropdown.alpine-list-checkbox
                    :id="$key"
                    :variable-name="$id"
                >
                    <x-input.includes.item-text
                        :id="$id"
                        :item="$item"
                        :key="$key"
                        :translation-key="$translationKey"
                        :item-lang-properties="$itemLangProperties"
                    />
                </x-general.dropdown.alpine-list-checkbox>
            @endforeach
        @endunless
    </x-general.dropdown.dropdown>

    @if ($multiple)
        <div
            x-show="$store['selectField{{ Str::studly($id) }}'].selectedItems.count > 0 && $store['selectField{{ Str::studly($id) }}'].selectedItems.count < Object.values({{ $id }}).length"
            class="flex flex-wrap gap-3 items-center mt-3"
        >
            @foreach ($items as $key => $item)
                <div
                    x-show="{{ $id }}.{{ $key }} === true"
                    class="inline-flex items-center p-2.5 space-x-2 text-sm font-semibold rounded border border-transparent cursor-pointer dark:text-white hover:text-white bg-theme-primary-100 text-theme-primary-600 transition-default group dark:border-theme-dark-600 dark:bg-theme-dark-800 hover:bg-theme-primary-700 hover:dark:bg-theme-dark-blue-700 hover:dark:border-theme-dark-blue-700"
                    @click="{{ $id }}.{{ $key }} = false"
                >
                    <div>
                        <x-input.includes.item-text
                            :id="$id"
                            :item="$item"
                            :key="$key"
                            :translation-key="$translationKey"
                            :item-lang-properties="$itemLangProperties"
                        />
                    </div>

                    <button
                        type="button"
                        class="p-1 group-hover:text-white text-theme-secondary-700 dark:text-theme-dark-200 group-hover:dark:text-white"
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
