@props(['wallet'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.livewire('transactions.{{ $wallet->publicKey() }}', 'NewTransaction', 'reloadTransactions');
            Webhook.livewire('transactions.{{ $wallet->address() }}', 'NewTransaction', 'reloadTransactions');
        });
    </script>
@endpush
