<div class="flex items-center justify-between">
    <div class="flex items-center max-w-full space-x-3">
        <a href="{{ route('wallet', $model->address()) }}" class="font-semibold link">
            {{ $model->username() }}
        </a>
        <span class="hidden min-w-0 sm:inline md:hidden lg:inline text-theme-secondary-400">
            <x-truncate-dynamic>{{ $model->address() }}</x-truncate-dynamic>
        </span>
    </div>
</div>
