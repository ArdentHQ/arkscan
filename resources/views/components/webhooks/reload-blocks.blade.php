@props(['publicKey'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.livewire('blocks.{{ $publicKey }}', 'NewBlock', 'reloadBlocks');
        });
    </script>
@endpush
