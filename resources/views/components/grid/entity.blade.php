<x-grid.generic :title="ucfirst($model->iconType())" icon="app-{{ $model->iconType() }}">
    <a href="{{ route('wallet', $model->recipient()->address()) }}" class="link">
        {{ $model->entityName() }}
    </a>
</x-grid.generic>
