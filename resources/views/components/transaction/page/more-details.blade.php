@props(['transaction'])

@php ($vendorField = $transaction->vendorField())

<div>
    <x-general.page-section.container
        :title="trans('pages.transaction.more_details')"
        class="sm:hidden mt-6"
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
    </x-general.page-section.container>

    <x-ark-container
        class="hidden sm:block"
        container-class="flex flex-col !py-0"
    >
        <div>
            <div class="font-semibold leading-5 text-theme-secondary-900 dark:text-theme-dark-50 dim:text-theme-dim-50">
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
        </div>
    </x-ark-container>
</div>
