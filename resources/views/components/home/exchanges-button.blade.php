@props([
    'buttonClass' => null,
])

@if (Network::canBeExchanged())
    <div {{ $attributes }}>
        <a
            href="{{ route('exchanges') }}"
            @class([
                'py-1.5 px-4 button button-secondary',
                $buttonClass,
            ])
        >
            <div class="inline-flex items-center space-x-2 lg:space-x-0 xl:space-x-2">
                <span>@lang('actions.exchanges')</span>

                <x-ark-icon
                    name="arrows.chevron-right-small"
                    size="xs"
                    class="lg:hidden xl:block"
                />
            </div>
        </a>
    </div>
@endif
