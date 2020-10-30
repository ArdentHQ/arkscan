<div class="flex">
    <div class="-mr-2 circled-icon text-theme-secondary-400 border-theme-danger-400">
        @svg($icon, 'w-5 h-5')
    </div>

    <div class="circled-icon text-theme-secondary-400 border-theme-secondary-700">
        <x-general.avatar-small :identifier="$model->address()" />
    </div>
</div>
