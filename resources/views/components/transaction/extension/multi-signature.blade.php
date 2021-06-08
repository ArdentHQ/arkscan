<div class="bg-white border-t border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
    <x-ark-container>
        <div class="w-full">
            <div class="flex relative justify-between items-end">
                <h3>@lang('pages.transaction.participants')</h3>
            </div>

            <x-ark-tables.table class="hidden mt-5 md:block">
                <thead>
                    <tr>
                        <x-tables.headers.desktop.id name="#" />
                        <x-tables.headers.desktop.address name="general.wallet.address" />
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->participants() as $participant)
                        <x-ark-tables.row>
                            <x-ark-tables.cell>
                                {{ $loop->index + 1}}
                            </x-ark-tables.cell>
                            <x-ark-tables.cell>
                                <x-tables.rows.desktop.address :model="$participant"/>
                            </x-ark-tables.cell>
                        </x-ark-tables.row>
                    @endforeach
                </tbody>
            </x-ark-tables.table>

            <div class="divide-y md:hidden table-list-mobile">
                @foreach($transaction->participants() as $participant)
                    <div class="table-list-mobile-row">
                        <div>
                            #<span>{{ $loop->index + 1}}</span>
                        </div>

                        <x-tables.rows.mobile.address :model="$participant" />
                    </div>
                @endforeach
            </div>
        </div>
    </x-ark-container>
</div>
