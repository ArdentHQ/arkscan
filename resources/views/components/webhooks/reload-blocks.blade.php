@props(['publicKey'])

@push('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            Webhook.listen('blocks.{{ $publicKey }}', 'NewBlock', 'reloadBlocks');
        });
    </script>
@endpush
