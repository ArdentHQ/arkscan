<x-grid.generic :title="trans('general.transaction.ipfs-hash')" icon="app-transactions.ipfs">
    <span class="flex items-center">
        <x-ark-external-link url="https://cloudflare-ipfs.com/ipfs/{{ $model->ipfsHash() }}">
            <x-slot name="text">
                <span class="sm:hidden md:inline lg:hidden">
                    <x-truncate-middle :length="10">
                        {{ $model->ipfsHash() }}
                    </x-truncate-middle>
                </span>

                <span class="hidden sm:inline md:hidden lg:inline">
                    <x-truncate-middle :length="30">
                        {{ $model->ipfsHash() }}
                    </x-truncate-middle>
                </span>
            </x-slot>
        </x-ark-external-link>
    </span>
</x-grid.generic>
