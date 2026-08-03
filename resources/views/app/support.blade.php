@component('layouts.app', ['fullWidth' => true])

    <x-metadata page="support" />

    @section('title', trans('metatags.contact.title'))

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.support.title')"
            :subtitle="trans('pages.support.description')"
        />

        <x-contact
            :email="config('mail.contact_email')"
            :page-title="trans('pages.support.title')"
            :page-description="trans('pages.support.description')"
            :help-description="trans('pages.support.let_us_help.description')"
            :documentation-url="trans('pages.support.docs')"
            :additional-description="trans('pages.support.additional')"
            :send-email-label="trans('pages.support.send_email')"
            :email-hint="trans('pages.support.email_hint')"
            :contact-networks="[
                'brands.x' => config('social.networks.twitter.url'),
                'brands.solid.github' => config('social.networks.github.url'),
            ]"
        />
    @endsection

@endcomponent
