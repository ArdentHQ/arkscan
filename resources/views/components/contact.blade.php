@props([
    'email' => config('mail.contact_email'),
    'discordUrl' => null,
    'socialIconHoverClass' => 'hover:bg-theme-secondary-300 hover:text-theme-secondary-900 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50',
    'documentationUrl' => trans('ui::urls.documentation'),
    'pageTitle' => trans('ui::pages.contact.title'),
    'pageDescription' => trans('ui::pages.contact.subtitle'),
    'helpTitle' => trans('ui::pages.contact.let_us_help.title'),
    'helpDescription' => trans('ui::pages.contact.let_us_help.description'),
    'additionalTitle' => trans('ui::pages.contact.additional_support.title'),
    'additionalDescription' => trans('ui::pages.contact.additional_support.description'),
    'sendEmailLabel' => trans('ui::actions.send'),
    'emailHint' => null,
    'contactNetworks' => [
        'brands.x' => trans('ui::urls.x'),
        'brands.facebook' => trans('ui::urls.facebook'),
        'brands.linkedin' => trans('ui::urls.linkedin'),
    ],
])

<div {{ $attributes }}>
    <x-general.mobile-divider />

    <div class="flex flex-col pb-8 mx-auto max-w-7xl md:px-10 lg:flex-row dark:text-theme-dark-200">
        <div class="flex flex-col flex-1 justify-between rounded-xl md:border lg:mr-1.5 lg:w-1/2 border-theme-secondary-300 dark:border-theme-dark-700">
            <div class="p-6">
                <div class="font-semibold md:text-lg text-theme-secondary-900 dark:text-theme-dark-50">{{ $helpTitle }}</div>

                <div class="mt-2 paragraph-description">
                    {{ $helpDescription }}
                </div>

                <div class="flex flex-col mt-4 space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">
                    <a
                        href="{{ $documentationUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="button-secondary"
                    >
                        @lang('ui::actions.documentation')
                    </a>

                    @if ($discordUrl)
                        <span class="font-semibold leading-none text-center">@lang('ui::general.or')</span>

                        <a href="{{ $discordUrl }}" target="_blank" rel="noopener nofollow noreferrer" class="button-secondary">
                            <div class="flex justify-center items-center space-x-2 w-full">
                                <x-ark-icon name="brands.discord" />
                                <span>@lang('ui::actions.discord')</span>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <hr class="mx-6 md:hidden border-theme-secondary-300 dark:border-theme-dark-700" />

            @if (count($contactNetworks) > 0)
                <div class="py-6 space-y-3 md:space-y-0 px-6 md:flex md:justify-between md:items-center rounded-b-xl text-theme-secondary-900 md:bg-theme-secondary-100 dark:text-theme-dark-200 dark:md:bg-theme-dark-950">
                    <div class="font-semibold md:text-lg text-theme-secondary-900 dark:text-theme-dark-50">
                        @lang('ui::pages.contact.social.subtitle')
                    </div>

                    <div class="flex space-x-3 text-theme-secondary-700 dark:text-theme-dark-300">
                        @foreach($contactNetworks as $name => $url)
                            <x-ark-social-square
                                class="w-10 h-10 !rounded border border-theme-secondary-300 dark:border-theme-dark-700"
                                hover-class="{{ $socialIconHoverClass }}"
                                :url="$url"
                                :icon="$name"
                                icon-size="w-4 h-4"
                            />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <x-general.mobile-divider />

        <div class="flex flex-col flex-1 p-6 rounded-xl md:mt-3 md:border lg:mt-0 lg:ml-1.5 border-theme-secondary-300 dark:border-theme-dark-700">
            <div class="mb-2 font-semibold md:text-lg text-theme-secondary-900 dark:text-theme-dark-50">
                {{ $additionalTitle }}
            </div>

            <div class="paragraph-description dark:text-theme-dark-300">
                {{ $additionalDescription }}
            </div>

            <div class="flex justify-between items-center px-4 mt-4 space-x-3 h-[45px] md:h-14 rounded border border-theme-secondary-300 bg-theme-secondary-100 dark:border-theme-dark-700 dark:bg-theme-dark-950">
                <div class="flex items-center space-x-2 min-w-0 text-theme-secondary-700 dark:text-theme-dark-300 leading-5.25">
                    <x-ark-icon
                        name="paper-plane"
                        size="sm"
                        class="flex-shrink-0"
                    />

                    <span class="font-semibold truncate text-lg">{{ $email }}</span>
                </div>

                <x-ark-clipboard
                    :value="$email"
                    class="md:button-secondary flex flex-shrink-0 md:h-8 md:px-4 md:py-1.5 items-center md:space-x-2"
                    no-styling
                >
                    <span class="hidden md:inline">@lang('actions.copy')</span>
                </x-ark-clipboard>
            </div>

            <a href="mailto:{{ $email }}" class="block mt-3 w-full button-primary bg-theme-blue-600 dark:!bg-theme-dark-blue-500">
                {{ $sendEmailLabel }}
            </a>

            @if ($emailHint)
                <p class="mt-2 text-xs text-theme-secondary-500 dark:text-theme-dark-500 font-semibold">
                    {{ $emailHint }}
                </p>
            @endif
        </div>
    </div>
</div>
