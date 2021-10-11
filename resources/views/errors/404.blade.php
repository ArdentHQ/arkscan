@component('layouts.app', ['containerClass' => 'flex items-center'])
    <x-metadata page="404" />

    @section('content')
        <div class="flex flex-col justify-center items-center space-y-8">
            <div class="flex justify-center w-full">
                <img src="/images/errors/404.svg" class="block max-w-4xl dark:hidden"/>
                <img src="/images/errors/404_dark.svg" class="hidden max-w-4xl dark:block"/>
            </div>

            <div class="text-lg font-semibold text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                {!! \ARKEcosystem\Foundation\UserInterface\UI::getErrorMessage(404) !!}
            </div>
            <div class="space-x-3">
                <a href="{{ route('home') }}" class="button-primary">@lang('menus.home')</a>
                <a href="{{ url()->current() }}" class="button-secondary">@lang('general.reload')</a>
            </div>
        </div>
    @endsection
@endcomponent
