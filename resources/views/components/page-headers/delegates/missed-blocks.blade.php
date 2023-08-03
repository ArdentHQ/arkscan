@props([
    'statistics',
])

{{-- TODO: missed blocks for 30 days - https://app.clickup.com/t/862k7j3m7 --}}
@php ($missedBlocks = Arr::get($statistics, 'performances.missed'))

<x-page-headers.delegates.header-item
    :title="trans('pages.delegates.missed-blocks.title')"
    :attributes="$attributes"
>
    <div class="flex space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
        <div class="flex items-center space-x-2">
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                @if ($missedBlocks)
                    {{ $missedBlocks }}
                @else
                    -
                @endif
            </span>

            <x-general.badge class="py-px">
                @lang('pages.delegates.x_delegates', ['count' => $missedBlocks])
            </x-general.badge>
        </div>

        <a
            class="pl-3 link text-sm md:text-base !leading-5"
            href="#"
        >
            @lang('actions.view')
        </a>
    </div>
</x-delegates.header-item>
