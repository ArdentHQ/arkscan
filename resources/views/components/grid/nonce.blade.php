<x-grid.generic :title="trans('general.transaction.nonce')" icon="app-nonce">
    <x-number>{{ $model->nonce() }}</x-number>
</x-grid.generic>
