@props([
    'subject' => null,
    'message' => null,
    'discordUrl' => null,
    'socialIconHoverClass' => 'hover:bg-theme-secondary-300 hover:text-theme-secondary-900 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50',
    'documentationUrl' => trans('ui::urls.documentation'),
    'pageTitle' => trans('ui::pages.contact.title'),
    'pageDescription' => trans('ui::pages.contact.subtitle'),
    'helpTitle' => trans('ui::pages.contact.let_us_help.title'),
    'helpDescription' => trans('ui::pages.contact.let_us_help.description'),
    'additionalTitle' => trans('ui::pages.contact.additional_support.title'),
    'additionalDescription' => trans('ui::pages.contact.additional_support.description'),
    'formTitle' => trans('ui::pages.contact.form.title'),
    'formDescription' => trans('ui::pages.contact.form.description'),
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

            <hr class="mx-6 border-theme-secondary-300 dark:border-theme-dark-700" />

            <div class="flex-1 p-6">
                <div class="font-semibold md:text-lg text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $additionalTitle }}
                </div>

                <div class="mt-2 paragraph-description">
                    {{ $additionalDescription }}
                </div>
            </div>

            <hr class="mx-6 md:hidden border-theme-secondary-300 dark:border-theme-dark-700" />

            @if (count($contactNetworks) > 0)
                <div class="p-6 space-y-3 rounded-b-xl text-theme-secondary-900 md:bg-theme-secondary-100 dark:text-theme-dark-200 dark:md:bg-theme-dark-950">
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

        <x-general.mobile-divider class="mb-6" />

        <div
            class="flex flex-col flex-1 px-6 rounded-xl md:py-6 md:mt-3 md:border lg:mt-0 lg:ml-1.5 border-theme-secondary-300 dark:border-theme-dark-700"
            x-data="{ subject: '{{ old('subject', $subject) }}' }"
        >
            <div class="mb-2 font-semibold md:text-lg text-theme-secondary-900 dark:text-theme-dark-50">
                {{ $formTitle }}
            </div>

            <div>
                {{ $formDescription }}
            </div>

            <form
                id="contact-form"
                method="POST"
                action="{{ route('contact') }}#contact-form"
                class="flex flex-col flex-1 space-y-3"
                enctype="multipart/form-data"
            >
                @csrf

                @honeypot

                <div class="flex flex-col space-y-3 lg:flex-col lg:space-y-3 lg:space-x-0 md-lg:flex-row md-lg:space-y-0 md-lg:space-x-3">
                    <div class="flex flex-col space-y-3 md:flex-row md:space-y-0 md:space-x-3 lg:flex-1 md-lg:flex-2">
                        <x-ark-input
                            name="name"
                            :label="trans('ui::forms.name')"
                            autocomplete="name"
                            class="flex-1"
                            input-class="h-14"
                            :value="old('name')"
                            :errors="$errors"
                        />

                        <x-ark-input
                            type="email"
                            name="email"
                            :label="trans('ui::forms.email')"
                            autocomplete="email"
                            class="flex-1"
                            input-class="h-14"
                            :value="old('email')"
                            :errors="$errors"
                        />
                    </div>

                    <x-ark-select
                        name="subject"
                        on-change="subject = $event.target.value"
                        :label="trans('ui::forms.subject')"
                        :errors="$errors"
                        class="md-lg:flex-1"
                        select-class="h-14"
                    >
                        @foreach(config('web.contact.subjects') as $contactSubject)
                            <option
                                value="{{ $contactSubject['value'] }}"
                                @if(old('subject', $subject) === $contactSubject['value']) selected @endif
                            >
                                {{ $contactSubject['label'] }}
                            </option>
                        @endforeach
                    </x-ark-select>
                </div>

                <x-ark-textarea
                    name="message"
                    :label="trans('ui::forms.message')"
                    rows="2"
                    class="w-full"
                    :errors="$errors"
                    :placeholder="trans('ui::pages.contact.message_placeholder')"
                >{{ old('message', $message) }}</x-ark-textarea>

                <div x-show="subject === 'job_application'" x-cloak>
                    <x-ark-input
                        type="file"
                        name="attachment"
                        :label="trans('ui::forms.attachment_pdf')"
                        class="w-full"
                        :errors="$errors"
                        accept="application/pdf"
                    />
                </div>

                <div class="flex relative flex-col flex-1 justify-end pt-1">
                    <button
                        type="submit"
                        x-data="{
                            success: {{ (flash()->level === 'success') ? 'true' : 'false' }},
                            error: {{ (flash()->level === 'error') ? 'true' : 'false' }}
                        }"
                        @if(flash()->message)
                            x-init="livewire.emit('toastMessage', ['{{ flash()->message }}', '{{ flash()->level }}'])"
                        @endif
                        x-cloak
                        class="button-primary"
                    >
                        @lang('ui::actions.send')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
