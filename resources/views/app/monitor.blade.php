@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.monitor.title')" />
        <meta property="og:description" content="@lang('metatags.monitor.description')">
    @endpush

    @section('content')
        <div class="dark:bg-theme-secondary-900">
            <div class="flex-col pt-16 space-y-6 content-container">
                <x-general.search.header-slim :title="trans('pages.monitor.title')" />

                <livewire:monitor-statistics />
            </div>
        </div>
    @endsection

@endcomponent
