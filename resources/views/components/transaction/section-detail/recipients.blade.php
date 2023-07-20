@props(['transaction'])

<span class="inline-flex items-center">
    @lang('pages.transaction.value.multiple_x', ['count' => $transaction->recipientsCount()])
</span>
