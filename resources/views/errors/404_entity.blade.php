@component('layouts.app', ['containerChildClass' => 'w-full bg-white dark:bg-theme-secondary-900 flex-grow flex items-center justify-center'])
    <x-metadata page="404" />

    @section('content')
        <div class="flex flex-col justify-center items-center">
            <div class="flex justify-center w-full">
                <img src="/images/errors/404_entity.svg" class="max-w-sm dark:hidden"/>
                <img src="/images/errors/404_entity_dark.svg" class="hidden max-w-sm dark:block"/>
            </div>

            <div class="mt-8 text-lg font-semibold text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                {{ $exception->getPrevious()->getCustomMessage() }}
            </div>

            @if(is_a($exception->getPrevious(), \App\Exceptions\WalletNotFoundException::class))
                <div class="leading-7 text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                    @lang('errors.wallet_not_found_details')
                </div>
            @endif

            <div class="flex flex-col mt-8 space-y-3 w-full sm:block sm:space-y-0 sm:space-x-3 sm:w-auto">
                <a href="{{ route('home') }}" class="button-primary">@lang('menus.home')</a>
                <a href="{{ url()->current() }}" class="button-secondary">@lang('general.reload')</a>
            </div>
        </div>
    @endsection
@endcomponent
