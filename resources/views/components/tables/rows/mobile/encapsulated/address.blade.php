@props([
    'model',
    'withoutClipboard' => false,
    'withoutLabel' => false,
])

<div>
    @unless ($withoutLabel)
        <span class="font-semibold">
            @lang('labels.address')
        </span>
    @endunless

    <span class="flex justify-center items-center space-x-2">
        <x-general.identity
            :model="$model"
            without-username
        />

        @unless ($withoutClipboard)
            <x-clipboard
                :value="$model->address()"
                :tooltip="trans('pages.wallet.address_copied')"
            />
        @endunless
    </span>
</div>
