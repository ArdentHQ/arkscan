<div class="flex justify-between items-center">
    <div class="flex items-center space-x-3 max-w-full">
        <a href="{{ route('wallet', $model->address()) }}" class="font-semibold link">
            {{ $model->username() }}
        </a>
        <span class="hidden min-w-0 font-semibold sm:inline md:hidden lg:inline text-theme-secondary-500 dark:text-theme-secondary-700">
            <x-truncate-dynamic>{{ $model->address() }}</x-truncate-dynamic>
        </span>
    </div>
</div>
