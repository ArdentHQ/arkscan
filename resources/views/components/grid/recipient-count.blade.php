<x-grid.generic :title="trans('general.transaction.recipient')" icon="wallet">
    <x-number>{{ $model->recipientsCount() }}</x-number> @lang('general.transaction.recipients')
</x-grid.generic>
