<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <x-ark-container>
        <div class="w-full">
            <div class="flex relative justify-between items-end">
                <h4>@lang('pages.transaction.participants')</h4>
            </div>

            <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
                <div class="grid grid-cols-1 grid-flow-row gap-6 gap-y-12 pt-8 mb-8 w-full md:grid-cols-2 xl:gap-y-4">
                    @foreach($transaction->participants() as $participant)
                        <x-details.address
                            :title="trans('general.transaction.participant', [$loop->index + 1])"
                            :transaction="$transaction"
                            :model="$participant"
                            icon="app-volume" />
                    @endforeach
                </div>
            </div>
        </div>
    </x-ark-container>
</div>
