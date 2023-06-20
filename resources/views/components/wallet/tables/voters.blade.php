@props(['wallet'])

<div
    x-show="tab === 'voters'"
    id="voters-list"
    class="w-full"
>
    <livewire:wallet-voter-table
        :public-key="$wallet->publicKey()"
        :username="$wallet->username()"
    />
</div>
