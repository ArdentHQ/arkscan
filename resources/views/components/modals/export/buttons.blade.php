@props([
    'filename',
    'successToast',
])

<div>
    <div
        x-show="! hasStartedExport"
        class="flex modal-buttons"
    >
        <button
            type="button"
            class="button-secondary"
            wire:click="closeModal"
        >
            @lang('actions.cancel')
        </button>

        <button
            type="button"
            class="flex justify-center items-center space-x-2 sm:py-1.5 sm:px-4 sm:mb-0 button-primary"
            x-bind:disabled="! canExport()"
            x-on:click="exportData"
        >
            <x-ark-icon
                name="arrows.underline-arrow-down"
                size="sm"
            />

            <span>@lang('actions.export')</span>
        </button>
    </div>

    <div
        x-show="hasStartedExport && exportStatus !== ExportStatus.Error"
        class="flex modal-buttons"
    >
        <button
            type="button"
            class="button-secondary"
            x-on:click="hasStartedExport = false"
            x-show="dataUri === null"
        >
            @lang('actions.back')
        </button>

        <button
            type="button"
            class="button-secondary"
            wire:click="closeModal"
            x-show="dataUri !== null"
        >
            @lang('actions.close')
        </button>

        <a
            x-bind:href="dataUri"
            class="flex items-center sm:py-0 sm:px-4 button-primary"
            :class="{
                disabled: dataUri === null
            }"
            download="{{ $filename }}.csv"
            x-on:click="Livewire.dispatch('toastMessage', {
                message: '{{ $successToast }}',
                type: 'success',
            })"
        >
            <div class="flex justify-center items-center space-x-2 h-full">
                <x-ark-icon
                    name="arrows.underline-arrow-down"
                    size="sm"
                />

                <span>@lang('actions.download')</span>
            </div>
        </a>
    </div>

    <div
        x-show="hasStartedExport && exportStatus === ExportStatus.Error"
        class="flex modal-buttons"
    >
        <button
            type="button"
            class="button-secondary"
            x-on:click="hasStartedExport = false"
        >
            @lang('actions.back')
        </button>

        <button
            type="button"
            class="button-primary"
            x-on:click="exportData"
        >
            <div class="flex justify-center items-center space-x-2 h-full">
                <x-ark-icon
                    name="arrows.underline-arrow-down"
                    size="sm"
                />

                <span>@lang('actions.retry')</span>
            </div>
        </button>
    </div>
</div>
