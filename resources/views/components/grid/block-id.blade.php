<x-grid.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <span class="flex items-center flex-1 min-w-0">

        <a href="{{ route('block', $model->blockId()) }}" class="max-w-full min-w-0 font-semibold link">
            <x-truncate-dynamic>{{ $model->blockId() }}</x-truncate-dynamic>
        </a>

        <x-clipboard :value="$model->blockId()" />
    </span>
</x-grid.generic>
