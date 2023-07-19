@props(['model'])

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

        <x-clipboard
            :value="$model->address()"
            :tooltip="trans('pages.wallet.address_copied')"
        />
    </span>
</div>
