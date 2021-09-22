<div x-data="{ publicKeyModalVisible: false }" class="flex-1 mt-4 w-full sm:mt-0 sm:w-auto">
    <button type="button" @click="publicKeyModalVisible = !publicKeyModalVisible"
        class="flex justify-center items-center px-3 w-full h-11 rounded cursor-pointer lg:flex-none lg:w-16 bg-theme-secondary-800 transition-default dark:text-theme-secondary-600 dark:hover:text-theme-secondary-200 hover:bg-theme-primary-600">
        <x-ark-icon name="key" />
    </button>

    <div @click.away="publicKeyModalVisible = false" x-show="publicKeyModalVisible" class="absolute right-0 left-0 mx-8 mt-4 w-auto bg-white rounded-xl shadow-lg lg:left-auto lg:mx-0 lg:mt-1 lg:mr-32 z-15 dark:shadow-lg-dark dark:bg-theme-secondary-900" x-cloak>
        <div class="flex flex-col p-6 space-y-2 leading-tight">
            <span class="text-sm font-semibold text-theme-secondary-400 dark:text-theme-secondary-700">@lang('pages.wallet.public_key.title')</span>
            <span class="flex font-semibold link">
                <span class="inline-block truncate">
                    {{ $publicKey }}
                </span>

                <x-clipboard :value="$publicKey" />
            </span>
        </div>
    </div>
</div>
