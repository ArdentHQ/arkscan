@props(['wallet'])

<div
    x-data="{
        init() {
            Webhook.listen('transactions.{{ $wallet->publicKey() }}', 'NewTransaction', 'reloadTransactions');
            Webhook.listen('transactions.{{ $wallet->address() }}', 'NewTransaction', 'reloadTransactions');
        },
    }"
></div>
