@props([
    'mobile' => false,
])

<x-general.dropdown.filter
    :mobile="$mobile"
    without-text
>
    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            :label="trans('tables.filters.recent-votes.select_all')"
            model="selectAllFilters.recent-votes"
        />

        <x-tables.filters.includes.group>
            <x-tables.filters.includes.checkbox
                name="vote"
                model="filters.recent-votes.vote"
                :label="trans('tables.filters.recent-votes.vote')"
            />

            <x-tables.filters.includes.checkbox
                name="unvote"
                model="filters.recent-votes.unvote"
                :label="trans('tables.filters.recent-votes.unvote')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
