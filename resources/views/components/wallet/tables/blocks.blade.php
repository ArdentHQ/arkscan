@props(['wallet'])

<div
    x-show="tab === 'blocks'"
    id="blocks-list"
    class="w-full"
>
    <livewire:wallet-block-table
        :public-key="$wallet->publicKey()"
        :username="$wallet->username()"
    />
</div>
