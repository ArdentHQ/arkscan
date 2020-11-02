<x-grid.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <span class="flex items-center">

        <a href="{{ route('block', $model->blockId()) }}" class="font-semibold sm:hidden md:inline lg:hidden link">
            <x-truncate-middle :value="$model->blockId()" :length="10" />
        </a>

        <a href="{{ route('block', $model->blockId()) }}" class="hidden font-semibold sm:inline md:hidden lg:inline link">
            <x-truncate-middle :value="$model->blockId()" :length="32" />
        </a>

        <x-clipboard :value="$model->blockId()" />
    </span>
</x-grid.generic>
