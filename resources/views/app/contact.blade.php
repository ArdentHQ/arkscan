@component('layouts.app', ['fullWidth' => true])

    <x-metadata page="contact" />

    @section('title', trans('metatags.contact.title'))

    @section('content')
        <x-ark-pages-contact
            :subject="$subject ?? null"
            :message="$message ?? null"
            :help-description="trans('pages.support.description')"
            :documentation-url="trans('pages.support.docs')"
            :additional-description="trans('pages.support.additional')"
            :contact-networks="[
                'brands.twitter' => config('social.networks.twitter.url'),
                'brands.github' => config('social.networks.github.url'),
            ]"
        />
    @endsection

@endcomponent
