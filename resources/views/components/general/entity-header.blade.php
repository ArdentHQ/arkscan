<div class="flex flex-col border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="p-8 bg-black dark:bg-theme-secondary-900 @if (isset($bottom)) rounded-t-lg @else rounded-lg @endif">
        <div class="flex flex-col {{ $isBlockPage ?? false ? 'md:flex-row' : 'space-y-8 lg:flex-row' }} lg:space-y-0 justify-between">
            <div class="flex md:space-x-4">
                <div class="items-center hidden md:flex">
                    {!! $logo !!}
                </div>

                <div class="flex flex-col justify-between flex-1 min-w-0 space-y-4 font-semibold lg:ml-4 md:space-y-0">
                    <div class="flex text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">{{ $title }}</div>

                    <div class="flex items-center space-x-2 leading-tight">
                        <span class="flex max-w-full text-theme-secondary-400 dark:text-theme-secondary-200">
                            <x-truncate-dynamic>{{ $value}}</x-truncate-dynamic>

                            <x-clipboard :value="$value" colors="text-theme-secondary-600 hover:text-theme-secondary-400" />
                        </span>
                    </div>
                </div>

                @if ($extraLogo ?? false)
                    <div class="flex items-center">
                        {{ $extraLogo }}
                    </div>
                @endif
            </div>

            @if ($extension ?? false)
                {{ $extension }}
            @endif
        </div>
    </div>

    @isset($bottom)
        <div class="p-8 border-t rounded-b-lg bg-theme-secondary-100 border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
            {{ $bottom }}
        </div>
    @endisset
</div>
