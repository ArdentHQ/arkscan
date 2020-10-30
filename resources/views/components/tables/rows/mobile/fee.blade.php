<div>
    @lang('labels.fee')

    <x-general.amount-fiat-tooltip>
        <x-slot name="amount">
            <x-currency>{{ $model->fee() }}</x-currency>
        </x-slot>

        <x-slot name="fiat">
            {{ $model->feeFiat() }}
        </x-slot>
    </x-general.amount-fiat-tooltip>
</div>
