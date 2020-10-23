@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.blocks.title')" />
        <meta property="og:description" content="@lang('metatags.blocks.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container md:px-8">
                <div x-cloak class="w-full">
                    <div class="relative flex items-center justify-between">
                        <h2 class="text-3xl sm:text-4xl">@lang('metatags.blocks.title')</h2>
                    </div>

                    <livewire:block-table />
                </div>
            </div>
        </div>
    @endsection

@endcomponent
