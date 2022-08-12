@props(['wallet'])

<a href="{{ $wallet->voteUrl() }}" target="_blank" class="button-primary ml-3 h-11 flex-grow sm:w-auto">
    <span class="flex space-x-2 whitespace-nowrap h-full items-center justify-center">
        <span>
            <x-ark-icon name="app-transactions.vote" size="sm" />
        </span>
        <span>@lang('actions.vote')</span>
    </span>
</a>
