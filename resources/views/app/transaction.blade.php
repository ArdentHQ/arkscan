@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.transaction.title')" />
        <meta property="og:description" content="@lang('metatags.transaction.description')">
    @endpush

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-general.breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.transaction')],
        ]" />
    @endsection

    @section('content')
        <x-transaction.header :transaction="$transaction" />

        {{-- TODO: use dynamic blade component based on type-string --}}
        <x-details.grid>
            @if($transaction->isTransfer())
                <x-transaction.details.transfer :transaction="$transaction" />
            @endif

            @if($transaction->isMultiPayment())
                <x-transaction.details.multi-payment :transaction="$transaction" />
            @endif

            @if($transaction->isMultiSignature())
                <x-transaction.details.multi-signature :transaction="$transaction" />
            @endif

            @if($transaction->isEntityRegistration())
                <x-transaction.details.entity-registration :transaction="$transaction" />
            @endif

            @if($transaction->isSelfReceiving())
                <x-transaction.details.self-receiving :transaction="$transaction" />
            @endif
        </x-details.grid>

        {{-- TODO: move to component and use dynamic blade component --}}
        @if($transaction->isMultiSignature())
            <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
                <div class="py-16 content-container md:px-8">
                    <div class="w-full">
                        <div class="relative flex items-end justify-between">
                            <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.participants')</h2>
                        </div>

                        <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
                            <div class="grid w-full grid-flow-row grid-cols-1 gap-6 pt-8 mb-16 md:grid-cols-2 gap-y-12 xl:gap-y-4">
                                @foreach($transaction->participants() as $participant)
                                    <x-details.address
                                        :title="trans('general.transaction.participant', [$loop->index + 1])"
                                        :transaction="$transaction"
                                        :address="$participant"
                                        icon="app-volume" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- TODO: move to component and use dynamic blade component --}}
        {{-- TODO: proper styling with green/red icon and avatar overlap --}}
        @if($transaction->isVoteCombination())
            <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
                <div class="py-16 space-x-4 content-container md:px-8">
                    {{-- Vote --}}
                    <div class="w-1/2">
                        <div class="relative flex items-end justify-between mb-8">
                            <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.vote')</h2>
                        </div>

                        <x-details.address
                            :title="trans('general.transaction.delegate')"
                            :transaction="$transaction"
                            :address="$transaction->voted()->address"
                            icon="app-volume" />
                    </div>

                    {{-- Unvote --}}
                    <div class="w-1/2">
                        <div class="relative flex items-end justify-between mb-8">
                            <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.unvote')</h2>
                        </div>

                        <x-details.address
                            :title="trans('general.transaction.delegate')"
                            :transaction="$transaction"
                            :address="$transaction->unvoted()->address"
                            icon="app-volume" />
                    </div>
                </div>
            </div>
        @endif

        {{-- TODO: move to component and use dynamic blade component --}}
        @if($transaction->isMultiPayment())
            <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
                <div class="py-16 content-container md:px-8">
                    <div class="w-full">
                        <div class="relative flex items-end justify-between mb-8">
                            <h2 class="text-3xl sm:text-4xl">@lang('pages.transaction.recipient_list')</h2>
                        </div>

                        <x-tables.payments :payments="$transaction->payments()" />
                    </div>
                </div>
            </div>
        @endif
    @endsection

@endcomponent
