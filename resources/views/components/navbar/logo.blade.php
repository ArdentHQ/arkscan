<span class="flex relative items-center">
    <img src="/images/logo.svg" class="h-10 lg:h-11" />

    <span class="hidden ml-4 sm:flex sm:items-center sm:text-2xl text-theme-secondary-900 dark:text-theme-secondary-200">
        <span class="inline-flex">
            <span class="font-bold">
                @if(config('app.navbar_name'))
                    {{ config('app.navbar_name') }}
                @else
                    {{ Network::currency() }}
                @endif
            </span>

            <span class="uppercase">
                {{ trans('generic.scan') }}
            </span>
        </span>
    </span>
</span>
