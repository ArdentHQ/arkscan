<div class="flex flex-col border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="p-8 bg-black dark:bg-theme-secondary-900 @if (isset($bottom)) rounded-t-lg @else rounded-lg @endif">
        <div class="flex flex-col {{ $isBlockPage ?? false ? 'md:flex-row' : 'space-y-8 lg:flex-row' }} lg:space-y-0 justify-between">
            <div class="flex md:space-x-4">
                <div class="items-center hidden md:flex">
                    {!! $logo !!}
                </div>

                <div class="flex flex-col justify-between flex-1 space-y-4 font-semibold lg:ml-4 md:space-y-0">
                    <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">{{ $title }}</div>

                    <div class="flex items-center space-x-2 leading-tight">
                        <span class="flex text-theme-secondary-400 dark:text-theme-secondary-200">
                            <span class="hidden lg:inline-block">
                                {{ $value }}
                            </span>

                            <span class="lg:hidden">
                                <x-truncate-middle :value="$value" :length="16" />
                            </span>

                            <x-ark-clipboard :value="$value" class="flex items-center w-auto h-auto ml-2 text-theme-secondary-600" no-styling />
                        </span>
                    </div>
                </div>
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
