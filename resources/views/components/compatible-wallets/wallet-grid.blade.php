<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 md:mt-6 xl:grid-cols-5 md-lg:grid-cols-4">
    @foreach (trans('pages.compatible-wallets.wallets') as $wallet)
        <a
            href="{{ $wallet['url'] }}"
            target="_blank"
            rel="nofollow noopener noreferrer"
            class="flex flex-col bg-white rounded-xl border transition hover:cursor-pointer group border-theme-secondary-300 dark:bg-theme-dark-900 dark:border-theme-dark-700 dark:hover:bg-theme-secondary-800 hover:border-theme-primary-200 hover:bg-theme-primary-50"
        >
            <div class="flex justify-center items-center mx-2 mt-2 rounded-xl">
                <x-ark-icon name="app-wallets.{{$wallet['logo']}}" size="full" />
            </div>

            <div class="mx-6 mt-3 mb-6">
                <span class="inline font-semibold break-words transition text-theme-primary-600 dark:text-theme-dark-blue-400 dark:group-hover:text-theme-dark-blue-500 group-hover:text-theme-primary-700">
                    <span>{{ $wallet['title'] }}</span>
                    <x-ark-icon
                        name="arrows.arrow-external"
                        size="xs"
                        class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-secondary-500"
                    />
                </span>
            </div>
        </a>
    @endforeach
</div>
