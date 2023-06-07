<span class="flex relative items-center">
    <img src="/images/logo.svg" class="h-8" />

    <span class="hidden ml-4 md:flex md:items-center text-theme-secondary-900 dark:text-theme-secondary-200">
        <span class="inline-flex text-lg">
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
