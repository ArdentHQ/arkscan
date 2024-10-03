@extends('errors::layout', ['code' => 404])

@isset ($exception)
    @section('image')
        <x-ark-icon
            name="app-errors.generic"
            class="light-dark-icon"
            size="w-full"
        />
    @endsection

    @section('content')
        <x-ark-container class="flex mx-auto w-full md:items-center md:h-error">
            <div class="text-center">
                <div class="mx-auto w-84">
                    @yield('image')
                </div>

                <div class="mt-8 text-lg font-semibold text-center text-theme-secondary-900 dark:text-theme-dark-200">
                    {{ $exception->getPrevious()->getCustomMessage() }}
                </div>

                @if(is_a($exception->getPrevious(), \App\Exceptions\WalletNotFoundException::class))
                    <div class="mt-3 leading-7 text-center text-theme-secondary-900 dark:text-theme-dark-200">
                        @lang('errors.wallet_not_found_details')
                    </div>
                @endif

                <div class="flex flex-col mt-8 space-y-3 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('home') }}" class="button button-secondary">
                        @lang('ui::general.home')
                    </a>

                    <a href="{{ url()->current() }}" class="button button-primary">
                        @lang('general.reload')
                    </a>
                </div>
            </div>
        </x-ark-container>
    @endsection
@endisset
