@props(['transaction'])

<div class="flex flex-col space-y-2">
    <div class="text-xs leading-3.75 dark:text-theme-dark-200">
        <x-general.encapsulated.transaction-type :transaction="$transaction" />
    </div>

    @if ($transaction->isTransfer())
        <x-search.results.transaction-types.mobile.transfer :transaction="$transaction" />
    @elseif ($transaction->isVote() || $transaction->isUnvote() || $transaction->isVoteCombination())
        <x-search.results.transaction-types.mobile.vote :transaction="$transaction" />
    @elseif ($transaction->isMultiPayment())
        <x-search.results.transaction-types.mobile.multi-payment :transaction="$transaction" />
    @else
        <x-search.results.transaction-types.mobile.other :transaction="$transaction" />
    @endif
</div>
