@props([
    'hash',
])

<span class="flex items-center">
    <x-ark-external-link
        class="flex space-x-2 items-end"
        :url="trans('urls.ipfs', ['hash' => $hash])"
    >
        <x-slot name="text">
            <div class="md:hidden">
                <x-truncate-middle>{{ $hash }}</x-truncate-middle>
            </div>

            <div class="hidden md:block">
                <x-truncate-middle length="32">
                    {{ $hash }}
                </x-truncate-middle>
            </div>
        </x-slot>
    </x-ark-external-link>
</span>
