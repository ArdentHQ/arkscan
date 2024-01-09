@props([
    'options' => [],
    'initialValue' => '',
    'dispatchEvent' => null,
    'class' => 'w-full',
    'buttonClass' => 'inline-block w-full mr-10 px-4 py-3 text-left form-input transition-default dark:bg-theme-dark-900 dark:border-theme-dark-800',
    'wrapperClass' => 'w-full',
    'dropdownClass' => 'mt-1',
    'dropdownListClass' => 'max-h-80',
    'iconClass' => 'absolute inset-y-0 right-0 flex items-center justify-center mr-4',
    'placeholder' => '',
    'grouped' => false,
    'label' => null,
    'xData' => '{}',
    'height' => '320',
    'name' => '',
])

@php
$initialText = $grouped
    ? collect($options)->flatMap(fn ($value) => $value)->get($initialValue)
    : collect($options)->get($initialValue);
@endphp

<div class="input-group {{ $class }}">
    @if($label)
        <label
            for="{{ $name }}"
            class="input-label @if ($name) @error($name) input-label--error @enderror @endif"
        >
            {{ $label }}
        </label>
    @endif

    <div
        @class([
            'relative input-rich-select',
            $wrapperClass,
        ])
        x-data="RichSelect(
            {{ $xData }},
            {{ json_encode($options) }},
            '{{ $initialValue }}',
            '{{ $initialText }}',
            {{ $grouped ? 'true' : 'false'}}
            @if($dispatchEvent)
                , '{{ $dispatchEvent }}'
            @endif
        )"
    >
        <input
            x-ref="input"
            type="hidden"
            @input="onInput($dispatch, $event)"
            {{ $attributes }}

            @isset($initialValue)
                value="{{ $initialValue }}"
            @endisset
        />

        <button
            x-ref="button"
            @keydown.arrow-up.stop.prevent="onButtonClick()"
            @keydown.arrow-down.stop.prevent="onButtonClick()"
            @click="onButtonClick()"
            type="button"
            aria-haspopup="listbox"
            :aria-expanded="open"
            aria-labelledby="listbox-label"
            @class([
                'relative dropdown-button focus-visible:rounded',
                $buttonClass,
            ])
        >
            @isset($dropdownEntry)
                {{ $dropdownEntry }}
            @else
                <span
                    x-show="text"
                    x-text="text"
                    class="block truncate dark:text-theme-dark-200"
                ></span>

                <span
                    x-show="!text"
                    class="block truncate text-theme-secondary-500 dark:text-theme-dark-700"
                >
                    @if(isset($placeholder) && $placeholder)
                        {{ $placeholder }}
                    @else
                        &nbsp;
                    @endif
                </span>
            @endif

            <x-ark-chevron-toggle
                is-open="open"
                class="{{ $iconClass }} transform pointer-events-none"
                size="w-3 h-3"
            />
        </button>

        <div
            x-show="open"
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute w-full min-w-max z-10 dropdown {{ $dropdownClass }}"
            style="display: none;"
            @if ($height) data-height="{{ $height }}" @endif
        >
            <div
                @keydown.enter.stop.prevent="onOptionSelect()"
                @keydown.space.stop.prevent="onOptionSelect()"
                @keydown.escape="onEscape()"
                @keydown.arrow-up.prevent="onArrowUp()"
                @keydown.arrow-down.prevent="onArrowDown()"
                x-ref="listbox"
                role="listbox"
                aria-labelledby="listbox-label"
                class="custom-scroll py-3 overflow-auto bg-white rounded-md outline-none dark:bg-theme-dark-800 shadow-lg dark:text-theme-dark-200 hover:outline-none {{ $dropdownListClass }}"
                tabindex="-1"
            >
                @isset($dropdownList)
                    {{ $dropdownList }}
                @else
                    @if(!$grouped)
                    <template x-for="(optionValue, index) in Object.keys(options)" :key="optionValue">
                        <div
                            data-option
                            x-description="Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation."
                            x-state:on="Highlighted"
                            x-state:off="Not Highlighted"
                            :id="`listbox-option-${optionValue}`"
                            role="option"
                            @click="choose(optionValue)"
                            @mouseenter="selected = index"
                            @mouseleave="selected = null"
                            :class="{
                                'rich-select-dropdown-entry-selected': value === optionValue,
                                'rich-select-dropdown-entry-hover': selected === index && value !== optionValue,
                            }"
                            class="rich-select-dropdown-entry"
                            x-text="options[optionValue]"
                        ></div>
                    </template>
                    @else
                    <template x-for="(groupName, index) in Object.keys(options)" :key="index">
                        <div>
                            <span x-show="groupName" class="flex items-center px-8 pt-8 w-full text-sm font-bold leading-5 text-left text-theme-secondary-500" x-text="groupName"></span>

                            <template x-for="(optionValue, index2) in Object.keys(options[groupName])" :key="`${index}-${index2}`">
                                <div
                                    data-option
                                    :data-group="groupName"
                                    x-description="Select option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation."
                                    x-state:on="Highlighted"
                                    x-state:off="Not Highlighted"
                                    :id="`listbox-option-${optionValue}`"
                                    role="option"
                                    @click="choose(optionValue, groupName)"
                                    @mouseenter="selected = getOptionIndex(index, index2); selectedGroup = groupName"
                                    @mouseleave="selected = null; selectedGroup = null"
                                    :class="{
                                        'rich-select-dropdown-entry-selected': value === optionValue,
                                        'rich-select-dropdown-entry-hover': selected === getOptionIndex(index, index2) && value !== optionValue,
                                    }"
                                    class="rich-select-dropdown-entry"
                                    x-text="options[groupName][optionValue]"
                                ></div>
                            </template>

                            <hr x-show="index < Object.keys(options).length - 1" class="mx-8 mt-4 border-b border-dashed border-theme-dark-200" />
                        </div>
                    </template>
                    @endif
                @endif
            </div>
        </div>
    </div>

    @if (isset($name))
        <x-ark::inputs.input-error :name="$name" />
    @endif
</div>
