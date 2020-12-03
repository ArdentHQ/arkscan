<div class="flex flex-row items-center space-x-3 round-status-history">
    @foreach($model->performance() as $performed)
        @if($performed)
            <span class="text-theme-success-500 round-status">
                <x-ark-icon name="app-status-done" size="lg" />
            </span>
        @else
            <span class="text-theme-danger-500 round-status">
                <x-ark-icon name="app-status-undone" size="lg" />
            </span>
        @endif
    @endforeach
</div>
