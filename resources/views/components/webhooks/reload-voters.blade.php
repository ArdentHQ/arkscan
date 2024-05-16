@props(['publicKey'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.livewire('wallet-vote.{{ $publicKey }}', 'WalletVote', 'reloadVoters');
        });
    </script>
@endpush
