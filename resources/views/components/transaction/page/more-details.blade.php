@props(['transaction'])

@php ($vendorField = $transaction->vendorField())

<div>
    {{-- Mobile --}}
    <x-general.page-section.container
        :title="trans('pages.transaction.more_details')"
        class="mt-6 sm:hidden"
        wrapper-class="flex flex-col flex-1 space-y-3 whitespace-nowrap w-full"
    >
        <x-tables.rows.mobile content-class="divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-800 !space-y-3">
            <x-slot name="header" class="font-semibold">
                @lang('pages.transaction.gas_information')
            </x-slot>

            <x-tables.rows.mobile.encapsulated.cell :label="trans('pages.transaction.header.gas_limit')">
                64,004
            </x-tables.rows.mobile.encapsulated.cell>

            <x-tables.rows.mobile.encapsulated.cell
                :label="trans('pages.transaction.header.usage_by_transaction')"
                class="pt-3"
            >
                63,185
            </x-tables.rows.mobile.encapsulated.cell>
        </x-tables.rows.mobile>

        <x-tables.rows.mobile>
            <x-slot name="header" class="font-semibold">
                @lang('pages.transaction.other_attributes')
            </x-slot>

            <x-tables.rows.mobile.encapsulated.cell :label="trans('pages.transaction.header.position_in_block')">
                137
            </x-tables.rows.mobile.encapsulated.cell>
        </x-tables.rows.mobile>

        @if ($transaction->hasPayload())
            <div
                class="w-full space-y-3"
                x-data="{
                    isExpanded: false,
                    toggle() {
                        this.isExpanded = !this.isExpanded;
                    }
                }"
            >
                <div
                    class="w-full"
                    x-show="isExpanded"
                    x-cloak
                >
                    <x-transaction.code-block.code-block :transaction="$transaction" />
                </div>

                <button
                    class="link border-b border-dashed border-theme-primary-500 hover:no-underline hover:border-theme-primary-700 leading-5"
                    @click="toggle"
                >
                    <span x-show="!isExpanded">
                        @lang('actions.view_all')
                    </span>

                    <span x-show="isExpanded" x-cloak>
                        @lang('actions.hide')
                    </span>
                </button>
            </div>
        @endif
    </x-general.page-section.container>

    {{-- Desktop --}}
    <x-ark-container
        class="hidden sm:block"
        container-class="flex flex-col !py-0"
    >
        <div>
            <div class="font-semibold leading-5 text-theme-secondary-900 dim:text-theme-dim-50 dark:text-theme-dark-50">
                @lang('pages.transaction.more_details')
            </div>

            <x-general.page-section.container
                wrapper-class="max-w-full leading-7"
                class="mt-4 !px-0"
            >
                <div class="inline-block">
                    <x-general.badge class="text-sm">
                        @lang('pages.transaction.gas_information')
                    </x-general.badge>
                </div>

                <x-transaction.page.section-detail.row
                    :title="trans('pages.transaction.header.gas_limit')"
                    value="64,004"
                    :transaction="$transaction"
                />

                <x-transaction.page.section-detail.row
                    :title="trans('pages.transaction.header.usage_by_transaction')"
                    :transaction="$transaction"
                >
                    63,185
                </x-transaction.page.section-detail.row>
            </x-general.page-section.container>

            <x-general.page-section.container
                wrapper-class="max-w-full leading-7"
                class="!px-0"
            >
                <div class="inline-block">
                    <x-general.badge class="text-sm">
                        @lang('pages.transaction.other_attributes')
                    </x-general.badge>
                </div>

                <x-transaction.page.section-detail.row
                    :title="trans('pages.transaction.header.position_in_block')"
                    value="137"
                    :transaction="$transaction"
                />
            </x-general.page-section.container>

            @if ($transaction->hasPayload())
                <div x-data="{
                    isExpanded: false,
                    toggle() {
                        this.isExpanded = !this.isExpanded;
                    }
                }">
                    <x-general.page-section.container
                        class="!px-0"
                        x-show="isExpanded"
                        x-cloak
                    >
                        <x-transaction.code-block.code-block :transaction="$transaction" />
                    </x-general.page-section.container>

                    <x-general.page-section.container
                        wrapper-class="max-w-full"
                        class="!px-0"
                        no-border
                    >
                        <button
                            class="link border-b border-dashed border-theme-primary-500 hover:no-underline hover:border-theme-primary-700 leading-5"
                            @click="toggle"
                        >
                            <span x-show="!isExpanded">
                                @lang('actions.view_all')
                            </span>

                            <span x-show="isExpanded" x-cloak>
                                @lang('actions.hide')
                            </span>
                        </button>
                    </x-general.page-section.container>
                </div>
            @endif
        </div>
    </x-ark-container>
</div>
