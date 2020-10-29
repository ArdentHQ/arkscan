<div class="flex items-center justify-between space-x-3">
    <div class="flex items-center">
        <a href="{{ route('wallet', $model->address()) }}" class="font-semibold link">
            {{ $model->username() }}
        </a>
        <span class="ml-2 text-theme-secondary-400">
            <x-truncate-middle :value="$model->address()" :length="16" />
        </span>
    </div>
</div>
