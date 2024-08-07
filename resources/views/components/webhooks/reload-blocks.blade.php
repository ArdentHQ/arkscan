@props(['publicKey'])

<div
    x-data="{
        init() {
            Webhook.listen('blocks.{{ $publicKey }}', 'NewBlock', 'reloadBlocks');
        },
    }"
></div>
