@props(['publicKey'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.listen('wallet-vote.{{ $publicKey }}', 'WalletVote', 'reloadVoters');
        });
    </script>
@endpush
