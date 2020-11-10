<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 content-container md:px-8">
        <div class="w-full">
            <div class="relative flex items-end justify-between">
                <h4>@lang('pages.transaction.participants')</h4>
            </div>

            <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
                <div class="grid w-full grid-flow-row grid-cols-1 gap-6 pt-8 mb-16 md:grid-cols-2 gap-y-12 xl:gap-y-4">
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
    </div>
</div>
