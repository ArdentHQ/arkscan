{{-- @component('layouts.app', ['containerClass' => 'flex items-center'])
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
@endcomponent --}}



@extends('errors::layout', ['code' => 404])

@isset ($exception)
    @section('image')
        <x-ark-icon
            name="app-errors.generic"
            class="light-dark-icon"
            size="w-full h-full"
        />
    @endsection

    @section('content')
        <x-ark-container class="flex mx-auto w-full md:items-center md:h-error">
            <div class="text-center">
                <div class="mx-auto max-w-error-image">
                    @yield('image')
                </div>

                <div class="mt-8 text-lg font-semibold text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                    {{ $exception->getPrevious()->getCustomMessage() }}
                </div>

                @if(is_a($exception->getPrevious(), \App\Exceptions\WalletNotFoundException::class))
                    <div class="leading-7 text-center text-theme-secondary-900 dark:text-theme-secondary-600">
                        @lang('errors.wallet_not_found_details')
                    </div>
                @endif

                {{-- @if($maintenance ?? false)
                    <h1 class="px-2 mt-8 xl:px-0 header-2">
                        @lang('ui::errors.503_heading')
                    </h1>

                    <p class="px-8 mt-4 leading-loose dark:text-theme-secondary-500">
                        @lang('ui::errors.503_message')
                    </p>
                @else
                    <h1 class="mt-8 header-2">
                        @yield('heading', trans('ui::errors.heading'))
                    </h1>

                    <p class="mt-4 leading-loose dark:text-theme-secondary-500">
                        @yield('message', trans('ui::errors.message'))
                    </p>

                    <div class="flex flex-col mt-8 space-y-3 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-3">
                        @yield('buttons')
                    </div>
                @endif --}}
            </div>
        </x-ark-container>
    @endsection
@endisset
