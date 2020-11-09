<x-grid.generic :title="trans('general.transaction.ipfs-hash')" icon="app-transactions.ipfs">
    <span class="flex items-center">
        <x-ark-external-link url="https://cloudflare-ipfs.com/ipfs/{{ $model->ipfsHash() }}">
            <x-slot name="text">
                <span class="sm:hidden md:inline lg:hidden">
                    <x-truncate-middle :value="$model->ipfsHash()" :length="10" />
                </span>

                <span class="hidden sm:inline md:hidden lg:inline">
                    <x-truncate-middle :value="$model->ipfsHash()" :length="30" />
                </span>
            </x-slot>
        </x-ark-external-link>
    </span>
</x-grid.generic>