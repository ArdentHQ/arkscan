<x-grid.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <span class="flex flex-1 items-center min-w-0">

        <a href="{{ route('block', $model->blockId()) }}" class="min-w-0 max-w-full font-semibold link">
            <x-truncate-dynamic>{{ $model->blockId() }}</x-truncate-dynamic>
        </a>

        <x-clipboard :value="$model->blockId()" />
    </span>
</x-grid.generic>