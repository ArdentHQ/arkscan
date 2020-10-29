<div>
    @lang('labels.username')

    <div class="flex flex-row items-center space-x-3">
        <div class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
        <div class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
    </div>

    <x-general.identity :model="$model" />
</div>
