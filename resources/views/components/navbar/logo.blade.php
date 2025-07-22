<span class="flex relative items-center">
    <div class="flex space-x-0.5">
        @php ($navbarTag = config('arkscan.navbar.tag') ?: null) @endphp
        <img
            src="{{ Vite::image('logo.svg') }}"
            @class([
                'h-8 bg-theme-danger-400',
                'rounded-l-md' => $navbarTag !== null,
                'rounded-md' => $navbarTag === null,
            ])
        />

        @if ($navbarTag)
            <div
                class="flex rounded-r-md bg-theme-danger-100 text-theme-danger-600 dark:bg-theme-dark-800 dark:text-theme-dark-200"
                data-tippy-content="@lang('general.navbar.release_tag_tooltip', ['tag' => $navbarTag])"
            >
                <div class="flex items-center px-2 h-full text-xs font-semibold leading-none uppercase">
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
