@props(['wallet'])

<a href="{{ $wallet->voteUrl() }}" target="_blank" class="flex-grow ml-3 h-11 sm:w-auto button-primary">
    <span class="flex justify-center items-center space-x-2 h-full whitespace-nowrap">
        <span>
            <x-ark-icon name="app-transactions.vote" size="sm" />
        </span>
        <span>@lang('actions.vote')</span>
    </span>
</a>
