<div class="flex flex-row justify-end items-center space-x-1 w-full round-status-history md:justify-start">
    @foreach($model->performance() as $performed)
        @if($performed)
            <span class="text-theme-success-500 round-status">
                <x-ark-icon name="app-status-done" size="md" />
            </span>
        @else
            <span class="text-theme-danger-500 round-status">
                <x-ark-icon name="app-status-undone" size="md" />
            </span>
        @endif
    @endforeach
</div>
