@props([
    'title',
    'value',
    'model',
    'valueTitle',
])

<div class="flex flex-col sm:flex-row sm:justify-between pt-3 md:pt-1 xl:pt-0 font-semibold md:flex-row first:pt-4 first:md:pt-0">
    <div class="sm:flex-1 md:flex-none space-y-3 sm:space-y-0 sm:flex md:flex-col md:space-y-3 md-lg:flex-row md-lg:space-y-0 sm:justify-between md-lg:space-x-[66px]">
        <div class="space-y-2 md:space-y-0">
            <span class="md:hidden">
                {{ $title }}
            </span>

            <x-stats.insights.entity-column class="md:w-[260px] md-lg:w-[476px]">
                <x-slot name="title">
                    {{ $title }}
                </x-slot>

                @if (! $value)
                    <div class="dark:text-theme-dark-200">
                        @lang('general.na')
                    </div>
                @else
                    <a
                        href="{{ route('wallet', $model->model()) }}"
                        class="link"
                    >
                        {{ $model->username() }}
                    </a>
                @endif
            </x-stats.insights.entity-column>
        </div>

        @if ($value)
            <x-stats.insights.entity-column
                class="sm:w-[90px] md:w-[260px] xl:w-[180px]"
                :title="$valueTitle"
                show-on-mobile
            >
                @if (is_numeric($value))
                    <x-number>{{ $value }}</x-number>
                @else
                    {{ $value }}
                @endif
            </x-stats.insights.entity-column>
        @endif
    </div>
</div>
