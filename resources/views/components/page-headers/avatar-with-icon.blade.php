<div class="hidden lg:flex">
    <div class="-mr-2 circled-icon text-theme-secondary-400 border-theme-danger-400">
        <x-ark-icon :name="$icon" />
    </div>
</div>

<div class="hidden rounded-full border-4 border-theme-secondary-900 lg:flex">
    <div class="bg-theme-secondary-900 circled-icon text-theme-secondary-400 border-theme-secondary-700">
        <x-general.avatar-small :identifier="$model->address()" />
    </div>
</div>

<div
    class="flex bg-theme-secondary-900 lg:hidden circled-icon text-theme-secondary-400 border-theme-secondary-700 dark:bg-theme-secondary-900">
    <x-general.avatar-small :identifier="$model->address()" />
</div>
