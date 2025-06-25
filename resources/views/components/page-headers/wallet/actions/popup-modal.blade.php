@props([
    'value',
    'button',
    'title',
    'additionalButtons' => null,
])

<div
    x-data="{ modalVisible: false }"
    class="flex-1 ml-2 w-full"
    @keydown.document.escape="modalVisible = false"
    {{ $attributes }}
>
    <button
        type="button"
        @click="modalVisible = !modalVisible"
        {{ $button->attributes->class('p-2 w-full focus-visible:ring-inset button button-secondary button-icon') }}
    >
        {{ $button }}
    </button>

    <div
        x-show="modalVisible"
        class="flex absolute right-0 left-0 justify-between items-end p-6 mx-8 mt-4 space-x-4 w-auto bg-white rounded-xl border border-transparent shadow-lg md:mt-1 lg:mr-32 z-15 md-lg:left-auto dark:shadow-lg-dark dark:bg-theme-dark-900 dark:border-theme-dark-800"
        @click.outside="modalVisible = false"
        x-transition
        x-cloak
    >
        <div class="flex flex-col space-y-2 min-w-0 leading-tight">
            <span class="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-500">
                {{ $title }}
            </span>

            <span class="font-semibold text-theme-secondary-900 dark:text-theme-dark-200">
                <x-truncate-dynamic>{{ $value }}</x-truncate-dynamic>
            </span>
        </div>

        <div class="flex items-center space-x-2">
            <x-ark-clipboard
                :value="$value"
                class="flex items-center p-2 w-full h-auto group"
                wrapper-class="flex-1"
                :tooltip-content="trans('pages.wallet.copied_public_key')"
                with-checkmarks
                checkmarks-class="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
            />

            {{ $additionalButtons }}

            <button
                type="button"
                class="p-2 hover:text-white button button-generic dark:hover:text-white dark:text-theme-dark-500 hover:bg-theme-primary-700"
                @click="modalVisible = false"
            >
                <x-ark-icon
                    name="cross"
                    size="sm"
                />
            </button>
        </div>
    </div>
</div>
