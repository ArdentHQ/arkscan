@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        <x-ark-container>
            <div>
                <h1 class="text-2xl font-semibold xl:mb-1.5 text-theme-secondary-900">
                    @lang('pages.compatible-wallets.title')
                </h1>
                <span class="text-xs font-semibold">
                    @lang('pages.compatible-wallets.subtitle')
                </span>
            </div>

            <hr class="my-6 -mx-8 h-1 sm:hidden text-theme-secondary-200 bg-theme-secondary-200 dark:text-theme-secondary-800 dark:bg-theme-secondary-800" />

            <div class="flex flex-col w-full rounded-xl sm:mt-6 sm:border min-[960px]:flex-row border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="flex flex-col flex-1 justify-center pb-8 space-y-6 sm:py-8 sm:px-8">
                    <span class="text-sm items-center rounded font-semibold px-2 py-1 flex space-x-2 text-theme-secondary-900 dark:text-white dark:from-theme-navy-600 dark:via-theme-navy-600 dark:to-theme-navy-600 bg-gradient-to-r from-[#E5F0F8] via-[#FFF8EB] to-[#E5F0F8]">
                        <x-ark-icon name="circle.info" size="sm" class="shrink-0" />
                        <span>
                            @lang('pages.compatible-wallets.arkvault.disclaimer')
                        </span>
                    </span>
                    <div>
                        <h2 class="text-2xl font-semibold text-theme-secondary-900">
                            <span>@lang('general.arkvault') </span>
                            <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                                (@lang('pages.compatible-wallets.arkvault.web_wallet'))
                            </span>
                        </h2>
                        <p class="mt-2 leading-7 dark:text-theme-secondary-400">
                            @lang('pages.compatible-wallets.arkvault.description')
                        </p>
                    </div>
                    <div class="flex flex-col justify-between py-3 px-3 rounded-xl sm:flex-row sm:pl-6 bg-theme-primary-50 dark:bg-theme-dark-blue-800">
                        <div class="flex flex-1 items-center py-1 bg-no-repeat bg-right sm:dark:bg-[url('/images/wallets/arrows-dark.svg')] sm:bg-[url('/images/wallets/arrows.svg')] min-[960px]:bg-none lg:dark:bg-[url('/images/wallets/arrows-dark.svg')] lg:bg-[url('/images/wallets/arrows.svg')] mr-2">
                            <div>
                                <x-ark-icon name="app-wallets.arkvault" size="none" class="w-10 h-10 dark:text-white text-theme-navy-600" />
                            </div>
                            <div class="flex flex-col ml-3">
                                <span class="text-lg font-semibold dark:text-white text-theme-secondary-900">
                                    @lang('general.arkvault')
                                </span>
                                <span class="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-blue-400">
                                    @lang('pages.compatible-wallets.arkvault.subtitle')
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <a href="@lang('pages.compatible-wallets.arkvault.url')" target="_blank" rel="noopener nofollow noreferrer" class="flex items-center mt-4 w-full h-full rounded-xl sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary">
                                <div class="flex justify-center items-center h-full">
                                    <span>
                                        @lang('actions.learn_more')
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex flex-1 pt-2 pr-3 sm:py-2 grow">
                    <img src="{{ mix('images/wallets/arkvault.svg') }}" class="dark:hidden" />
                    <img src="{{ mix('images/wallets/arkvault-dark.svg') }}" class="hidden dark:block" />
                </div>
            </div>

            <hr class="my-6 -mx-8 h-1 sm:hidden text-theme-secondary-200 bg-theme-secondary-200 dark:text-theme-secondary-800 dark:bg-theme-secondary-800" />

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

            <div class="text-center sm:text-start flex-col sm:flex-row mt-6 w-full flex py-6 sm:py-2 px-6 justify-between items-center dark:from-theme-secondary-800 dark:via-theme-secondary-800 dark:to-theme-secondary-800 bg-gradient-to-r from-[#E5F0F8] via-[#FFF8EB] to-[#E5F0F8] rounded-xl">
                <span class="font-semibold sm:text-lg dark:text-white text-theme-primary-900">
                    @lang('pages.compatible-wallets.get_listed')
                </span>
                <button type="button" class="mt-3 w-full sm:mt-0 sm:w-auto button-primary">
                    @lang('actions.submit_wallet')
                </button>
            </div>
        </x-ark-container>
    @endsection
@endcomponent
