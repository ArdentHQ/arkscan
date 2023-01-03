<x-ark-container>
    <div class="flex bg-theme-primary-100 dark:bg-black md:space-x-8 rounded-xl p-6 sm:p-8">
        <div class="flex flex-1 flex-col justify-center">
            <div class="flex flex-col space-y-3 md:space-y-2">
                <h3>
                    <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                        @lang('pages.migration.banner.title')
                    </span>
                </h3>

                <div class="dark:text-theme-secondary-500">
                    @lang('pages.migration.banner.description', [
                        'link' => view('ark::external-link', [
                            'url'   => trans('urls.migration.guide'),
                            'text'  => 'Migration Guide',
                        ]),
                    ])
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0 pt-8">
                <a
                    href="@lang('urls.migration.migrate')"
                    class="button-primary"
                    target="_blank"
                >
                    @lang('actions.migrate_tokens')
                </a>

                <a
                    href="@lang('urls.migration.learn_more')"
                    class="button-secondary bg-theme-primary-200"
                    target="_blank"
                >
                    @lang('actions.learn_more')
                </a>
            </div>
        </div>

        <div class="hidden md:flex items-center">
            <x-ark-icon
                name="app-migration.banner"
                class="light-dark-icon"
                size="w-64 lg:w-auto"
            />
        </div>
    </div>
</x-ark-container>
