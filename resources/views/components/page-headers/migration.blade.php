<x-ark-container container-class="pb-5">
    <div class="flex p-6 rounded-xl sm:p-8 md:space-x-8 dark:bg-theme-secondary-900 bg-theme-primary-100">
        <div class="flex flex-col flex-1 justify-center">
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
                            'text'  => trans('actions.migration_guide'),
                        ]),
                    ])
                </div>
            </div>

            <div class="flex flex-col pt-8 space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
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

        <div class="hidden items-center md:flex">
            <x-ark-icon
                name="app-migration.banner"
                class="light-dark-icon"
                size="w-64 lg:w-116 transition-default"
            />
        </div>
    </div>
</x-ark-container>
