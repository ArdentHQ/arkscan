<div class="flex">
    <div class="-mr-2 circled-icon text-theme-secondary-400 border-theme-danger-400">
        @svg($icon, 'w-5 h-5')
    </div>

    <div class="-mt-1 border-4 border-black rounded-full dark:border-theme-secondary-900">
        <div class="bg-black circled-icon text-theme-secondary-400 border-theme-secondary-700 dark:bg-theme-secondary-900">
            <x-general.avatar-small :identifier="$model->address()" />
        </div>
    </div>
</div>
