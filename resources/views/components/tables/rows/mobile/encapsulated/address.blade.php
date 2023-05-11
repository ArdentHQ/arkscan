<div>
    <span class="font-semibold">
        @lang('labels.address')
    </span>

    <span class="flex justify-center items-center space-x-2">
        <x-general.identity :model="$model" without-username without-reverse />
        <x-ark-clipboard :value="$model->address()" class="mr-3 transition text-theme-primary-400 dark:text-theme-secondary-600 hover:text-theme-primary-700" no-styling />
    </span>
</div>
