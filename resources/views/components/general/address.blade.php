<div class="flex items-center space-x-3">
    <x-general.avatar :identifier="$address" />

    <a href="{{ route('wallet', $address) }}" class="font-semibold link">
        <x-truncate-middle :value="$address" />
    </a>
</div>
