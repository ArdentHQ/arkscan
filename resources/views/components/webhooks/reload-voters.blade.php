@props(['publicKey'])

<div
    x-data="{
        init() {
            Webhook.listen('wallet-vote.{{ $publicKey }}', 'WalletVote', 'reloadVoters');
        },
    }"
></div>
