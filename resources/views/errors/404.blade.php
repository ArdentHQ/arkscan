@component('layouts.app', ['containerClass' => 'flex items-center'])
    @push('metatags')
        <meta property="og:title" content="404 - Error | ARK Documentation" />
    @endpush

    @section('breadcrumbs')
        <x-general.breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.error.404')],
        ]" />
    @endsection

    @section('content')
        <div class="flex flex-col items-center justify-center space-y-8">
            <img src="/images/errors/404.svg" class="max-w-4xl"/>
            <div class="text-lg font-semibold text-center text-theme-secondary-900">
                {!! ARKEcosystem\UserInterface\UI::getErrorMessage(404) !!}
            </div>
            <div class="space-x-3">
                <a href="{{ route('home') }}" class="button-primary">@lang('menus.home')</a>
                <a href="{{ url()->current() }}" class="button-secondary">@lang('general.reload')</a>
            </div>
        </div>
    @endsection
@endcomponent
