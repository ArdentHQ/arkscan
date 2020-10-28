<div class="flex flex-row items-center space-x-3">
    <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
    <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
</div>

<x-general.address :address="$model->username()" />
