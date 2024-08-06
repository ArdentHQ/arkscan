@props(['publicKey'])

@if (config('broadcasting.default') === 'reverb')
    <div
        x-data="{
            init() {
                Webhook.listen('wallet-vote.{{ $publicKey }}', 'WalletVote', 'reloadVoters');
            },
        }"
    ></div>
@endif
