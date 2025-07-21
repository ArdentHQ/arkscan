<span class="flex relative items-center">
    <div class="flex space-x-0.5">
        @php ($navbarTag = config('arkscan.navbar.tag') ?: null) @endphp
        <img
            src="{{ Vite::image('logo.svg') }}"
            @class([
                'h-8 bg-theme-danger-400',
                'rounded-l-lg' => $navbarTag !== null,
                'rounded-lg' => $navbarTag === null,
            ])
        />

        @if ($navbarTag)
            <div
                class="flex bg-theme-danger-100 dark:bg-theme-dark-800 rounded-r-lg text-theme-danger-600 dark:text-theme-dark-200"
                data-tippy-content="@lang('general.navbar.release_tag_tooltip', ['tag' => $navbarTag])"
            >
                <div class="flex leading-none items-center text-xs font-semibold px-2 h-full uppercase">
                    {{ $navbarTag }}
                </div>
            </div>
        @endif
    </div>

    <span class="hidden ml-4 md:flex md:items-center text-theme-secondary-900 dark:text-theme-dark-50">
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
