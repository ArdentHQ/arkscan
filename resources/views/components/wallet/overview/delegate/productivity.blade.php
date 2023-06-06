@props(['wallet'])

@if($wallet->productivity() >= 0)
    <x-percentage>
        {{ $wallet->productivity() }}
    </x-percentage>
@endif
