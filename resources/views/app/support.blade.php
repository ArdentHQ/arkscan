@component('layouts.app', ['fullWidth' => true])

    <x-metadata page="support" />

    @section('title', trans('metatags.contact.title'))

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.support.title')"
            :subtitle="trans('pages.support.description')"
        />

        <x-contact
            :subject="$subject ?? null"
            :message="$message ?? null"
            :page-title="trans('pages.support.title')"
            :page-description="trans('pages.support.description')"
            :help-description="trans('pages.support.let_us_help.description')"
            :documentation-url="trans('pages.support.docs')"
            :additional-description="trans('pages.support.additional')"
            :contact-networks="[
                'brands.x' => config('social.networks.twitter.url'),
                'brands.solid.github' => config('social.networks.github.url'),
            ]"
            :form-title="trans('pages.support.form.title')"
        />
    @endsection

@endcomponent
