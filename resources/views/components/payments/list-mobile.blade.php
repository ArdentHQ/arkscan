<div class="space-y-8 divide-y md:hidden">
    @foreach ($payments as $payment)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <div class="flex justify-between w-full">
                @lang('general.transaction.recipient')

                <div class="flex flex-row items-center space-x-3">
                    <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                    <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                </div>

                <x-general.address :address="$payment['recipientId']" />
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.amount')

                <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>

                <div wire:loading.class="hidden">
                    {{ $payment['amount'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>
