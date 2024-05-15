@props(['publicKey'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.livewire('voters.{{ $publicKey }}', 'NewVote', 'reloadVoters');
        });
    </script>
@endpush
