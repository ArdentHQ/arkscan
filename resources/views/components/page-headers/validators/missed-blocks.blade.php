@props([
    'missedBlocks',
    'validatorsMissed',
])

<x-page-headers.header-item
    :title="trans('pages.validators.missed-blocks.title')"
    :attributes="$attributes"
>
    <div class="flex space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
        <div class="flex items-center space-x-2">
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                @if ($missedBlocks)
                    <x-number>{{ $missedBlocks }}</x-number>
                @else
                    -
                @endif
            </span>

            <x-general.badge class="py-px text-theme-secondary-700">
                {{ trans_choice('pages.validators.x_validators', $validatorsMissed) }}
            </x-general.badge>
        </div>

        <button
            x-data
            type="link"
            class="pl-3 link text-sm md:text-base !leading-5"
            x-on:click="() => {
                Livewire.dispatch('showValidatorsView', {view: 'missed-blocks'});
                scrollToQuery('#missed-blocks-list');
            }"
        >
            @lang('actions.view')
        </button>
    </div>
</x-page-headers.header-item>
