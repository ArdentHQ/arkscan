<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex-wrap py-16 content-container md:px-8">
        <div class="w-full mb-8">
            <h2 class="text-xl sm:text-2xl">@lang('pages.wallet.registrations')</h2>
        </div>

        <div class="flex flex-col w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
            <div class="grid w-full grid-flow-row grid-cols-1 gap-6 md:grid-cols-2 gap-y-12 xl:gap-y-4">
                @foreach($wallet->registrations() as $registration)
                    {{-- @TODO: translation --}}
                    <x-details.generic :title="ucfirst($registration->iconType())" icon="app-volume">
                        <a href="{{ route('wallet', $registration->recipient()->address()) }}" class="link">
                            {{ $registration->entityName() }}
                        </a>
                    </x-details.generic>
                @endforeach
            </div>
        </div>
    </div>
</div>
