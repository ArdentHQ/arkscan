<x-grid.generic :title="trans('general.transaction.fee')" icon="app-fee">
    <x-currency :currency="Network::currency()">{{ $model->fee() }}</x-currency>
</x-grid.generic>
