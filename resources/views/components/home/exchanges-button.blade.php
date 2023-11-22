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
            @lang('actions.exchanges')
        </a>
    </div>
@endif
