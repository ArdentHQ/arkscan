@props([
    'title',
    'items',
    'width' => 'w-[87px]',
])

<div {{ $attributes->class('px-3 sm:px-6 md:px-10 md:mx-auto md:max-w-7xl group last:mb-8 dark:text-theme-dark-200') }}>
    <div class="flex mt-6 sm:mt-0 sm:space-x-3 group-first:mt-0 group-first:sm:-mt-2">
        <div class="hidden flex-col ml-3 sm:flex w-[1.625rem]">
            <div class="hidden -mt-2 w-full border-l-2 sm:block group-first:sm:block border-theme-secondary-300 h-[9px] dark:border-theme-dark-700"></div>

            <div class="hidden w-full rounded-bl-xl border-b-2 border-l-2 sm:block border-theme-secondary-300 min-h-[12px] dark:border-theme-dark-700"></div>

            <div class="hidden flex-1 w-full border-l-2 sm:block group-last:hidden border-theme-secondary-300 min-h-[12px] dark:border-theme-dark-700"></div>
        </div>

        <div class="flex flex-col flex-1 space-y-3 font-semibold sm:pb-4 sm:space-y-2">
            <div class="py-2 px-3 border-l-2 sm:py-0 sm:px-0 sm:bg-transparent sm:border-0 bg-theme-secondary-100 border-theme-primary-400 dark:sm:bg-transparent dark:bg-theme-dark-950 dark:border-theme-dark-blue-400">
                {{ $title }}
            </div>

            <div class="flex px-3 space-x-4 text-sm sm:py-4 sm:px-6 sm:text-base sm:leading-5 sm:rounded-xl sm:border leading-[17px] sm:border-theme-secondary-300 dark:border-theme-dark-700">
                @if (is_array($items))
                    <div @class([
                        'flex flex-col space-y-3 whitespace-nowrap',
                        $width,
                    ])>
                        @foreach (array_keys($items) as $dataTitle)
                            <span>{{ $dataTitle }}</span>
                        @endforeach
                    </div>

                    <div class="flex flex-col flex-1 space-y-3 text-right sm:text-left text-theme-secondary-900 dark:text-theme-dark-50">
                        @foreach ($items as $dataContent)
                            @if (empty($dataContent))
                                <span class="font-base">
                                    @lang('general.na')
                                </span>
                            @elseif (! is_array($dataContent))
                                <span>
                                    {{ $dataContent }}
                                </span>
                            @elseif (is_array($dataContent) && array_key_exists('content', $dataContent))
                                <span @class($dataContent['class'] ?? null)>
                                    {{ $dataContent }}
                                </span>
                            @elseif (array_key_exists('component', $dataContent))
                                <span @class($dataContent['class'] ?? null)>
                                    {!! Blade::render($dataContent['component'], $dataContent['data'] ?? []) !!}
                                </span>
                            @endif
                        @endforeach
                    </div>
                @else
                    <span>{{ $items }}</span>
                @endif
            </div>
        </div>
    </div>
</div>
