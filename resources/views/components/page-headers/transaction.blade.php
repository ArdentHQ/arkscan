<div class="dark:bg-theme-secondary-900">
    <x-ark-container container-class="flex flex-col space-y-6">
        <x-general.search.header-slim :title="trans('pages.transaction.title')" />

        <x-general.entity-header
            :title="trans('pages.transaction.transaction_id')"
            :value="$transaction->id()"
        >
            <x-slot name="logo">
                <x-page-headers.circle>
                    <span class="text-lg font-medium">ID</span>
                </x-page-headers.circle>
            </x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-dynamic-component :component="$transaction->headerComponent()" :transaction="$transaction" />
                </div>
            </x-slot>
        </x-general.entity-header>
    </x-ark-container>
</div>
