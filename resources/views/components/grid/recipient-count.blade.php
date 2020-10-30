<x-grid.generic :title="trans('general.transaction.recipient')" icon="app-volume">
    <x-number>{{ $model->recipientsCount() }}</x-number> @lang('general.transaction.recipients')
</x-grid.generic>
