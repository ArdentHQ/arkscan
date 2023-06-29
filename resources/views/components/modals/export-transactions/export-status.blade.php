<div class="flex flex-col">
    <div class="flex justify-center mb-6">
        <div x-show="exportStatus === 'PENDING_DOWNLOAD'">
            <img src="{{ mix('images/modals/export/loading.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/modals/export/loading-dark.svg') }}" class="hidden dark:block" />
        </div>

        <div x-show="exportStatus === 'DONE'">
            <img src="{{ mix('images/modals/export/success.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/modals/export/success-dark.svg') }}" class="hidden dark:block" />
        </div>

        <div x-show="exportStatus === 'ERROR'">
            <img src="{{ mix('images/modals/export/error.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/modals/export/error-dark.svg') }}" class="hidden dark:block" />
        </div>

        <div x-show="exportStatus === 'WARNING'">
            <img src="{{ mix('images/modals/export/error.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/modals/export/error-dark.svg') }}" class="hidden dark:block" />
        </div>
    </div>

    <div class="mb-4">
        <div x-show="exportStatus === 'PENDING_DOWNLOAD'">
            <x-ark-alert
                :title="trans('general.information')"
                :message="trans('general.export.information_text')"
            />
        </div>

        <div x-show="exportStatus === 'DONE'">
            <x-ark-alert
                :title="trans('general.success')"
                type="success"
            >
                <span x-text="successMessage"></span>
            </x-ark-alert>
        </div>

        <div x-show="exportStatus === 'ERROR'">
            <x-ark-alert
                :title="trans('general.error')"
                type="error"
            >
                <span x-text="errorMessage"></span>
            </x-ark-alert>
        </div>

        <div x-show="exportStatus === 'WARNING'">
            <x-ark-alert
                :title="trans('general.warning')"
                :message="trans('general.export.warning_text')"
                type="warning"
            />
        </div>
    </div>

    <div class="border border-theme-secondary-300 dark:border-theme-dark-500 px-4 rounded flex justify-between items-center">
        <div class="flex items-center space-x-2 text-theme-secondary-900 dark:text-theme-dark-50 font-semibold h-12 min-w-0">
            <x-ark-icon
                name="app-csv"
                class="fill-theme-primary-600 dark:fill-theme-dark-blue-500"
            />

            <div
                x-text="`${address.substr(0, 5)}...${address.substr(-5)}.csv`"
                class="truncate"
            ></div>
        </div>

        <div class="relative">
            <div
                x-show="exportStatus === 'PENDING_DOWNLOAD'"
                class="w-8 h-8"
            >
                <x-ark-loader-icon
                    path-class="fill-theme-primary-600 dark:fill-theme-dark-blue-400"
                    circle-class="stroke-theme-primary-100 dark:stroke-theme-dark-700"
                />
            </div>

            <div
                x-show="exportStatus !== 'PENDING_DOWNLOAD'"
                class="flex flex-shrink-0 justify-center items-center w-6 h-6 rounded-full"
                :class="{
                    'bg-theme-primary-100 dark:bg-theme-dark-blue-500': ['DONE', 'WARNING'].includes(exportStatus),
                    'bg-theme-danger-100 dark:bg-theme-danger-400': exportStatus === 'ERROR',
                }"
            >
                <div x-show="['DONE', 'WARNING'].includes(exportStatus)">
                    <x-ark-icon
                        name="check-mark-small"
                        size="w-2.5 h-2.5"
                        class="text-theme-primary-600 dark:text-theme-dark-50"
                    />
                </div>

                <div x-show="exportStatus === 'ERROR'">
                    <x-ark-icon
                        name="cross-small"
                        size="w-2.5 h-2.5"
                        class="text-theme-danger-400 dark:text-theme-dark-50"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
