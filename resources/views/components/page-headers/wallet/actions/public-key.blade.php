@props([
    'publicKey',
    'button' => null,
])

<div
    x-data="{ publicKeyModalVisible: false }"
    class="flex-1 ml-2 w-full"
    {{ $attributes }}
>
    @if ($button)
        <button
            type="button"
            @click="publicKeyModalVisible = !publicKeyModalVisible"
            {{ $button->attributes }}
        >
            {{ $button }}
        </button>
    @else
        <button
            type="button"
            @click="publicKeyModalVisible = !publicKeyModalVisible"
            class="text-theme-secondary-600 hover:text-theme-secondary-400"
        >
            <x-ark-icon name="key" />
        </button>
    @endif

    <div
        x-show="publicKeyModalVisible"
        class="flex absolute right-0 left-0 items-end p-6 mx-8 mt-4 space-x-4 w-auto bg-white rounded-xl shadow-lg lg:left-auto lg:mx-0 lg:mt-1 lg:mr-32 z-15 dark:shadow-lg-dark dark:bg-theme-secondary-900"
        @click.outside="publicKeyModalVisible = false"
        x-transition
        x-cloak
    >
        <div class="flex flex-col space-y-2 leading-tight">
            <span class="text-sm font-semibold text-theme-secondary-400 dark:text-theme-secondary-700">
                @lang('pages.wallet.public_key.title')
            </span>

            <span class="flex font-semibold">
                <span class="inline-block truncate">
                    {{ $publicKey }}
                </span>
            </span>
        </div>

        <div class="flex items-center space-x-2">
            <x-ark-clipboard
                :value="$publicKey"
                class="flex items-center p-2 w-full h-auto"
                wrapper-class="flex-1"
                :tooltip-content="trans('pages.wallet.copied_public_key')"
                with-checkmarks
                checkmarks-class="text-theme-primary-900 dark:text-theme-secondary-200"
            />

            <button
                type="button"
                class="p-2 button button-generic hover:bg-theme-primary-700"
                @click="publicKeyModalVisible = false"
            >
                <x-ark-icon
                    name="cross"
                    size="sm"
                />
            </button>
        </div>
    </div>
</div>
