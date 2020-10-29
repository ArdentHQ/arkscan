<div class="flex items-center justify-between space-x-3">
    <div class="flex items-center">
        @if ($prefix ?? false)
            {{ $prefix }}
        @endif

        <a href="{{ route('wallet', $model->address()) }}" class="font-semibold link">
            @if ($model->username())
                {{ $model->username() }}
            @else
                <x-truncate-middle :value="$model->address()" />
            @endif
        </a>
    </div>
</div>
