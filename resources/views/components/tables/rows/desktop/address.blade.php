<x-general.identity :model="$model" :without-truncate="$withoutTruncate ?? false">
    @if ($model->username())
        <x-slot name="suffix">
            <span class="hidden ml-1 font-semibold lg:flex text-theme-secondary-500">
                <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
            </span>
        </x-slot>
    @else
        <x-slot name="address">
            <span class="lg:hidden">
                <x-truncate-middle>{{ $model->address() }}</x-truncate-middle>
            </span>
            <span class="hidden lg:inline">
                {{ $model->address() }}
            </span>
        </x-slot>
    @endif
</x-general.identity>
