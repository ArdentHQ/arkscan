@props([
    'selected',
    'options' => [],
])

<div {{ $attributes->only('class') }}>
    {{--mobile: input select--}}
    <label class="relative lg:hidden">
        <div wire:ignore>
            <x-rich-select
                wire:model="{{ $attributes->wire('model')->value() }}"
                wrapper-class="relative left-0 w-full xl:inline-block sm:w-[150px]"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="inline-block w-full text-left !px-3 !py-2 form-input transition-default dark:bg-theme-dark-900 dark:border-theme-dark-700 !text-sm font-semibold"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>
    </label>

    {{--desktop: buttons group--}}
    <div class="hidden relative z-0 lg:inline-flex">
        <x-tabs.wrapper
            class="px-2"
            default-selected="{{ $selected }}"
            on-selected="function (value) {
                this.$wire.set('period', value);
            }"
        >
            @foreach($options as $val => $label)
                @unless ($loop->first)
                    <div class="ml-2 w-px h-4 bg-theme-secondary-300 dark:bg-theme-dark-700"></div>
                @endunless

                <x-tabs.tab
                    :name="$val"
                    :class="Arr::toCssClasses(['pl-2' => ! $loop->first,
                    ])"
                >
                    {{ $label }}
                </x-tabs.tab>
            @endforeach
        </x-tabs.wrapper>
    </div>
</div>
