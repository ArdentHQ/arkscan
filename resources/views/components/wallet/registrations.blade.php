<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex-wrap py-16 content-container md:px-8">
        <div class="w-full mb-8">
            <h4>@lang('pages.wallet.registrations')</h4>
        </div>

        <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
            <div class="grid w-full grid-flow-row grid-cols-1 gap-x-6 md:grid-cols-2 gap-y-4 details-grid">
                @foreach($wallet->registrations() as $registration)
                    <x-grid.entity :model="$registration" />
                @endforeach
            </div>
        </div>
    </div>
</div>
