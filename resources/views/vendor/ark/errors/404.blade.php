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
            </div>
        </x-ark-container>
    @endsection
@endisset
