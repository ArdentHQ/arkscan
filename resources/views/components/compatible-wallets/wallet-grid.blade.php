<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:mt-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
    @foreach (trans('pages.compatible-wallets.wallets') as $wallet)
        <div class="flex flex-col bg-white rounded-xl border border-theme-secondary-300 dark:bg-theme-secondary-900 dark:border-theme-secondary-800">
            <div class="flex justify-center items-center mx-2 mt-2 rounded-xl aspect-square">
                <x-ark-icon name="app-wallets.{{$wallet['logo']}}" size="none" />
            </div>
            <div class="mx-6 mt-3 mb-6">
                <x-ark-external-link :url="$wallet['url']">
                    {{ $wallet['title'] }}
                </x-ark-external-link>
            </div>
        </div>
    @endforeach
</div>
