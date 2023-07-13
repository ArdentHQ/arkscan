@props([
    'address',
])

<span class="flex items-center">
    <a
        href="{{ route('wallet', $address) }}"
        class="link"
    >
        {{ $address }}
    </a>

    <x-clipboard
        :value="$address"
        colors="text-theme-secondary-600 hover:text-theme-secondary-400"
    />
</span>
