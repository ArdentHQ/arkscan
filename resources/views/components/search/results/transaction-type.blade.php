@if ($transaction->isTransfer())
    <x-search.results.transaction-types.transfer :transaction="$transaction" />
@elseif ($transaction->isVote() && ! $transaction->isVoteCombination())
    <x-search.results.transaction-types.vote :transaction="$transaction" />
@elseif ($transaction->isMultiPayment())
    <x-search.results.transaction-types.multi-payment :transaction="$transaction" />
@else
    <x-search.results.transaction-types.other :transaction="$transaction" />
@endif
