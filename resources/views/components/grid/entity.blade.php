<x-grid.generic :title="ucfirst($model->iconType())" icon="app-transactions.{{ $model->iconType() }}">
    <a href="{{ route('transaction', $model->id()) }}" class="link">
        {{ $model->entityName() }}
    </a>
</x-grid.generic>
