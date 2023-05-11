<x-general.identity :model="$model" :without-truncate="$withoutTruncate ?? false" :without-username="$withoutUsername ?? false">
    <x-slot name="address">
        <span class="xl:hidden">
            <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
        </span>
        <span class="hidden xl:inline">
            {{ $model->address() }}
        </span>
    </x-slot>
</x-general.identity>
