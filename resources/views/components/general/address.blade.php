<div class="flex items-center space-x-3">
    <x-general.avatar :identifier="$address" />

    <a href="{{ route('wallet', $address) }}" class="font-semibold link">{{ truncateMiddle($address) }}</a>
</div>
