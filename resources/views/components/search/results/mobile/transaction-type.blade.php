@props(['transaction'])

<div class="flex flex-col space-y-2">
    <div class="text-xs leading-3.75 dark:text-theme-dark-200">
        <x-general.encapsulated.transaction-type :transaction="$transaction" />
    </div>

    @if ($transaction->isTransfer())
        <x-search.results.transaction-types.transfer :transaction="$transaction" />
    @elseif ($transaction->isVote() || $transaction->isUnvote())
        <x-search.results.transaction-types.vote :transaction="$transaction" />
    @elseif ($transaction->isMultiPayment())
        <x-search.results.transaction-types.multi-payment :transaction="$transaction" />
    @else
        <x-search.results.transaction-types.other :transaction="$transaction" />
    @endif
</div>
