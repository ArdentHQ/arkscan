@props([
    'title',
    'subtitle',
    'icon',
    'titleExtra' => null,
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950',
    'padding' => 'py-3 px-3 mt-6 md:px-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600',
    'iconSize' => 'w-11 h-11',
    'iconColor' => 'dark:text-white text-theme-navy-600',
    'buttonColor' => 'dim:bg-theme-dark-blue-600',
    'arrowsBreakpoint' => null,
    'arrowsClass' => [],
    'mobileTall' => false,
])

<div {{ $attributes->class([
    'flex flex-col justify-between rounded-xl sm:flex-row',
    $padding,
    $backgroundColor,
]) }}>
    <div @class([
        'flex flex-1 items-center bg-no-repeat bg-right mx-auto sm:ml-0 sm:mr-2',
        'flex-col text-center' => $mobileTall,
        ...$arrowsClass,
    ])>
        <div>
            <x-ark-icon
                :name="$icon"
                :size="$iconSize"
                :class="$iconColor"
            />
        </div>

        <div class="flex flex-col justify-center ml-3 space-y-2">
            <span @class([
                'text-lg leading-5.25 font-semibold leading-6',
                'flex flex-col mt-3' => $mobileTall,
                $titleColor,
            ])>
                <span>{{ $title }}</span>

                @if ($titleExtra)
                    <span class="text-theme-secondary-600 dark:text-theme-secondary-200">
                        {{ $titleExtra }}
                    </span>
                @endif
            </span>

            <span @class([
                'text-xs font-semibold leading-3.75',
                $subtitleColor,
            ])>
                {{ $subtitle }}
            </span>
        </div>
    </div>

    <div class="flex items-center mt-4 sm:mt-0 sm:h-auto">
        <a
            href="@lang('pages.compatible-wallets.arkvault.url')"
            target="_blank"
            rel="noopener nofollow noreferrer"
            @class(['flex items-center py-3.5 w-full rounded-lg sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary sm:h-15', $buttonColor])
        >
            <div class="flex justify-center items-center h-full text-lg leading-5.25">
                <span>
                    @lang('actions.learn_more')
                </span>
            </div>
        </a>
    </div>
</div>
