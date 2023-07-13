@props([
    'title',
    'items',
])

<div {{ $attributes->class('flex sm:space-x-3 px-6 md:px-10 md:mx-auto md:max-w-7xl group') }}>
    <div class="hidden flex-col ml-3 sm:flex w-[26px]">
        <div class="hidden -mt-2 border-l-2 sm:block group-first:sm:block w-[26px] border-theme-secondary-300 h-[9px]"></div>

        <div class="hidden rounded-bl-xl border-b-2 border-l-2 sm:block w-[26px] border-theme-secondary-300 min-h-[12px]"></div>

        <div class="hidden flex-1 border-l-2 sm:block group-last:hidden w-[26px] border-theme-secondary-300 min-h-[12px]"></div>
    </div>

    <div class="flex flex-col flex-1 space-y-3 font-semibold sm:pb-4 sm:space-y-2">
        <div class="py-2 px-3 border-l-2 sm:py-0 sm:px-0 sm:bg-transparent sm:border-0 bg-theme-secondary-100 border-theme-primary-400">
            {{ $title }}
        </div>

        <div class="flex space-x-4 text-sm sm:py-4 sm:px-6 sm:text-base sm:leading-5 sm:rounded-xl sm:border leading-[17px] sm:border-theme-secondary-300">
            @if (is_array($items))
                <div class="flex flex-col space-y-3 whitespace-nowrap w-[87px]">
                    @foreach (array_keys($items) as $dataTitle)
                        <span>{{ $dataTitle }}</span>
                    @endforeach
                </div>

                <div class="flex flex-col flex-1 space-y-3 text-right sm:text-left">
                    @foreach ($items as $dataContent)
                        @if (empty($dataContent))
                            <span class="text-theme-secondary-900 font-base">
                                @lang('general.na')
                            </span>
                        @elseif (! is_array($dataContent))
                            <span class="text-theme-secondary-900">
                                {{ $dataContent }}
                            </span>
                        @elseif (array_key_exists('component', $dataContent))
                            {!! Blade::render($dataContent['component'], $dataContent['data'] ?? []) !!}
                        @endif
                    @endforeach
                </div>
            @else
                <span>{{ $items }}</span>
            @endif
        </div>
    </div>
</div>
