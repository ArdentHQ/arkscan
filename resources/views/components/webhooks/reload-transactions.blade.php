@props(['wallet'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.listen('transactions.{{ $wallet->publicKey() }}', 'NewTransaction', 'reloadTransactions');
            Webhook.listen('transactions.{{ $wallet->address() }}', 'NewTransaction', 'reloadTransactions');
        });
    </script>
@endpush
