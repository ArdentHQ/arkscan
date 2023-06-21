<div>
    <span class="font-semibold">
        @lang('labels.address')
    </span>

    <span class="flex justify-center items-center space-x-2">
        <x-general.identity
            :model="$model"
            without-username
            without-reverse
            without-icon
        />

        <x-ark-clipboard
            :value="$model->address()"
            :tooltip-content="trans('pages.wallet.address_copied')"
            class="transition text-theme-primary-400 dark:text-theme-secondary-600 hover:text-theme-primary-700"
            no-styling
        />
    </span>
</div>
