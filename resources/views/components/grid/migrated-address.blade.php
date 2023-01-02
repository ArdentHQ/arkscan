@props ([
    'model',
])

<div class="flex justify-between items-center pb-4 border-b border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col flex-1 space-y-2 min-w-0">
        <div class="flex items-center">
            <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">
                @lang ('general.transaction.migrated_address')
            </span>
        </div>

        <span class="font-semibold text-theme-secondary-700">
            <x-ark-external-link url="https://polygonscan.com/address/{{ $model->migratedAddress() }}">
                <x-slot name="text">
                    <span class="sm:hidden md:inline lg:hidden">
                        <x-truncate-middle :length="12">
                            {{ $model->migratedAddress() }}
                        </x-truncate-middle>
                    </span>

                    <span class="hidden sm:inline md:hidden lg:inline">
                        {{ $model->migratedAddress() }}
                    </span>
                </x-slot>
            </x-ark-external-link>
        </span>
    </div>

    <div
        class="flex justify-center items-center w-11 h-11 rounded-full"
        x-data="{
            init() {
                const img = new Image
                img.src = makeBlockie('{{ $model->migratedAddress() }}')
                img.classList.add('rounded-full')

                $root.appendChild(img)
            }
        }"
    >
    </div>
</div>
