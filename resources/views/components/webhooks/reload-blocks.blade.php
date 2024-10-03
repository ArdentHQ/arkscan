@props(['publicKey'])

@if (config('broadcasting.default') === 'reverb')
    <div
        x-data="{
            init() {
                Webhook.listen('blocks.{{ $publicKey }}', 'NewBlock', 'reloadBlocks');
            },
        }"
    ></div>
@endif
