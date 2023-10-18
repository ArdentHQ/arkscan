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
                wrapper-class="relative left-0 xl:inline-block w-full sm:w-[150px]"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="inline-block w-full text-left !px-3 !py-2 form-input transition-default dark:bg-theme-secondary-900 dark:border-theme-secondary-800 !text-sm font-semibold leading-4.25"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>
    </label>

    {{--desktop: buttons group--}}
    <div class="hidden relative z-0 lg:inline-flex">
        <x-tabs.wrapper
            class="pr-4"
            default-selected="{{ $selected }}"
            on-selected="function (value) {
                this.$wire.set('period', value);
            }"
        >
            @foreach($options as $val => $label)
                <x-tabs.tab :name="$val">
                    {{ $label }}
                </x-tabs.tab>
            @endforeach
        </x-tabs.wrapper>
    </div>
</div>
