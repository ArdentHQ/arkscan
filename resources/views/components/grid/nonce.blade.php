<x-grid.generic :title="trans('general.transaction.nonce')" icon="app-nonce" :without-border="$withoutBorder ?? false">
    <x-number>{{ $model->nonce() }}</x-number>
</x-grid.generic>
