<x-grid.generic :title="trans('general.transaction.timestamp')" icon="app-timestamp" :without-border="$withoutBorder ?? false">
    {{ $model->timestamp() }}
</x-grid.generic>
