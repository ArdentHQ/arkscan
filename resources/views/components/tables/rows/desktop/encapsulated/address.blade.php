@props([
    'model',
    'withoutTruncate' => false,
    'withoutUsername' => false,
])

<span class="flex justify-between w-full">
    <span>
        <x-general.identity :model="$model" :without-truncate="$withoutTruncate" :without-username="$withoutUsername">
            <x-slot name="address">
                <span class="xl:hidden">
                    <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
                </span>
                <span class="hidden xl:inline">
                    {{ $model->address() }}
                </span>
            </x-slot>
        </x-general.identity>
    </span>
    <x-ark-clipboard :value="$model->address()" class="mr-3 transition text-theme-primary-400 dark:text-theme-secondary-600 hover:text-theme-primary-700" no-styling />
</span>
