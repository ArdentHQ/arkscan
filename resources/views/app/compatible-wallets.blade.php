@component('layouts.app')
    <x-metadata page="wallets" />

    @section('content')
        <x-ark-container>
            <div>
                <h1 class="font-semibold text-theme-secondary-900 text-2xl">@lang('pages.compatible-wallets.title')</h1>
                <span class="text-sm">@lang('pages.compatible-wallets.subtitle')</span>
            </div>

            <div class="w-full flex flex-col min-[960px]:flex-row rounded-xl border border-theme-secondary-300 dark:border-theme-secondary-800 mt-6">
                <div class="flex flex-col justify-between flex-1 py-8 px-8 space-y-6">
                    <span class="items-center rounded font-semibold px-2 py-1 flex space-x-2 text-theme-secondary-900 dark:text-white dark:from-theme-primary-400 dark:via-theme-primary-400 dark:to-theme-primary-400 bg-gradient-to-r from-[#E5F0F8] via-[#FFF8EB] to-[#E5F0F8]">
                        <x-ark-icon name="circle.info" />
                        <span>@lang('pages.compatible-wallets.arkvault.disclaimer')</span>
                    </span>
                    <div class="">
                        <h2 class="font-semibold text-theme-secondary-900 text-2xl">@lang('general.arkvault') <span class="text-theme-secondary-500 dark:text-theme-secondary-200">(@lang('pages.compatible-wallets.arkvault.web_wallet'))</span></h2>
                        <p class="leading-7 dark:text-theme-secondary-400 mt-2">@lang('pages.compatible-wallets.arkvault.description')</p>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between rounded-xl bg-theme-primary-50 pl-6 pr-3 py-3">
                        <div class="flex flex-1 items-center py-1 bg-no-repeat bg-right sm:bg-[url('/images/wallets/arrows.svg')] min-[960px]:bg-none lg:bg-[url('/images/wallets/arrows.svg')] mr-2">
                            <div><x-ark-icon name="app.wallets-arkvault" size="none" class="w-10 h-10" /></div>
                            <div class="ml-3 flex flex-col">
                                <span class="font-semibold text-theme-secondary-900 text-lg">@lang('general.arkvault')</span>
                                <span class="text-theme-secondary-700 text-sm font-semibold">@lang('pages.compatible-wallets.arkvault.subtitle')</span>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <a href="@lang('pages.compatible-wallets.arkvault.url')" target="_blank" rel="noopener nofollow noreferrer" class="flex items-center mt-4 w-full h-full rounded-xl sm:mt-0 sm:w-auto md:w-full md:mt-0 lg:w-auto button-primary"><div class="flex justify-center items-center h-full"><span>@lang('actions.learn_more')</span></div></a>
                        </div>
                    </div>
                </div>
                <div class="flex flex-1 py-2 pr-3">
                    <img src="{{ mix('images/wallets/arkvault.svg') }}" />
                </div>
            </div>

            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 mt-6">
                @foreach (trans('pages.compatible-wallets.wallets') as $wallet)
                    <div class="flex flex-col rounded-xl border border-theme-secondary-300 bg-white dark:bg-theme-secondary-900 dark:border-theme-secondary-800">
                        <div class="flex items-center justify-center rounded-xl aspect-square mx-2 mt-2"> <x-ark-icon name="app.wallets-{{$wallet['logo']}}" size="none" /></div>
                        <div class="mt-3 mx-6 mb-6">
                            <x-ark-external-link :url="$wallet['url']">{{ $wallet['title'] }}</x-ark-external-link>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center sm:text-start flex-col sm:flex-row mt-6 w-full flex py-6 sm:py-2 px-6 justify-between items-center dark:from-theme-secondary-800 dark:via-theme-secondary-800 dark:to-theme-secondary-800 bg-gradient-to-r from-[#E5F0F8] via-[#FFF8EB] to-[#E5F0F8] rounded-xl">
                <span class="dark:text-white font-semibold text-lg text-theme-primary-900">@lang('pages.compatible-wallets.get_listed')</span>
                <button type="button" class="button-primary w-full sm:w-auto mt-3 sm:mt-0">@lang('actions.submit_wallet')</button>
            </div>
        </x-ark-container>
    @endsection
@endcomponent
