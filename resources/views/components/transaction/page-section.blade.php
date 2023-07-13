@props([
    'title',
    'items',
])

<div {{ $attributes->class('flex sm:space-x-3 px-6 md:px-10 md:mx-auto md:max-w-7xl group') }}>
    <div class="hidden ml-3 w-[26px] sm:flex flex-col">
        <div class="hidden group-first:sm:block w-[26px] border-l-2 border-theme-secondary-300 sm:block -mt-2 h-[9px]"></div>

        <div class="w-[26px] border-b-2 border-l-2 border-theme-secondary-300 rounded-bl-xl hidden sm:block min-h-[12px]"></div>

        <div class="group-last:hidden w-[26px] border-l-2 border-theme-secondary-300 hidden sm:block min-h-[12px] flex-1"></div>
    </div>

    <div class="flex flex-col space-y-3 sm:space-y-2 font-semibold flex-1 sm:pb-4">
        <div class="bg-theme-secondary-100 border-l-2 border-theme-primary-400 sm:border-0 sm:bg-transparent py-2 px-3 sm:py-0 sm:px-0">
            {{ $title }}
        </div>

        <div class="flex space-x-4 sm:border sm:border-theme-secondary-300 sm:rounded-xl sm:py-4 sm:px-6 text-sm sm:text-base leading-[17px] sm:leading-5">
            @if (is_array($items))
                <div class="flex flex-col space-y-3 w-[87px] whitespace-nowrap">
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
