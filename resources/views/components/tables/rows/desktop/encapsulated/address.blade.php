@props([
    'model',
    'withoutTruncate' => false,
    'withoutUsername' => false,
    'withoutClipboard' => false,
])

<span class="flex justify-between w-full text-sm leading-[17px]">
    <span>
        <x-general.identity
            :model="$model"
            :without-truncate="$withoutTruncate"
            :without-username="$withoutUsername"
            without-icon
        >
            <x-slot name="address">
                @unless ($withoutTruncate)
                    <span class="xl:hidden">
                        <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
                    </span>
                    <span class="hidden xl:inline">
                        {{ $model->address() }}
                    </span>
                @else
                    <span class="inline">
                        {{ $model->address() }}
                    </span>
                @endif
            </x-slot>
        </x-general.identity>
    </span>

    @unless ($withoutClipboard)
        <x-ark-clipboard :value="$model->address()" class="mr-3 transition text-theme-primary-400 dark:text-theme-secondary-600 hover:text-theme-primary-700" no-styling />
    @endunless
</span>
