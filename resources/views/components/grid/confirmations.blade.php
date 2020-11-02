<x-grid.generic :title="trans('general.transaction.confirmations')" icon="app-confirmations">
    <x-number>{{ $model->confirmations() }}</x-number>
</x-grid.generic>
