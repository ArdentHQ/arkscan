@props([
    'selected',
    'options' => [],
])

<div {{ $attributes->only('class') }}>

    {{--mobile: input select--}}
    <label class="relative sm:hidden">
        <div wire:ignore>
            <x-ark-rich-select
                wire:model="{{ $attributes->wire('model')->value() }}"
                wrapper-class="relative left-0 xl:inline-block"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="block mr-10 text-sm font-semibold text-left bg-transparent text-theme-secondary-700 dark:text-theme-secondary-200"
                icon-class="flex absolute inset-y-0 right-0 justify-center items-center -mr-4"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>
    </label>

    {{--tablet: dropdown--}}
    <div class="hidden sm:block md:hidden">
        <x-ark-dropdown
            wrapper-class="relative p-2 mb-8 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800"
            button-class="p-3 w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    <div>
                        <div x-show="dropdownOpen !== true">
                            <x-ark-icon name="menu" size="sm" />
                        </div>

                        <div x-show="dropdownOpen === true">
                            <x-ark-icon name="menu-show" size="sm" />
                        </div>
                    </div>

                    <div>@lang('forms.statistics.periods.'.$selected)</div>
                </div>
            </x-slot>

            <div class="block justify-center items-center py-3 mt-1">
                @foreach($options as $val => $label)
                    <a
                        wire:click="$set('period', '{{ $val }}');"
                        class="dropdown-entry @if($selected === $val) dropdown-entry-selected @endif"
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </x-ark-dropdown>
    </div>

    {{--desktop: buttons group--}}
    <div class="hidden relative z-0 md:inline-flex">
        <x-tabs.wrapper
            default-selected="{{ $selected }}"
            on-selected="function (value) {
                this.$wire.set('period', value);
            }"
        >
            @foreach($options as $val => $label)
                <x-tabs.tab :name="$val">
                    <span>{{ $label }}</span>
                </x-tabs.tab>
            @endforeach

            <x-slot name="right">
                <div class="w-8 h-auto"></div>
            </x-slot>
        </x-tabs.wrapper>
    </div>
</div>
