<div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
<div wire:loading.class="hidden">
    <div class="flex flex-row items-center space-x-3 pl-14">
        @foreach($model->performance() as $performed)
            @if($performed)
                <span class="text-theme-success-500">
                    @svg('app-status-done', 'w-8 h-8')
                </span>
            @else
                <span class="text-theme-danger-500">
                    @svg('app-status-undone', 'w-8 h-8')
                </span>
            @endif
        @endforeach
    </div>
</div>
