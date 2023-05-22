<x-exchanges.dropdown
    :title="trans('pages.exchanges.type.title')"
    icon="app-stack"
>
    <x-general.dropdown.list-item>
        @lang('general.all')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item>
        @lang('pages.exchanges.type.exchanges')
    </x-general.dropdown.list-item>

    <x-general.dropdown.list-item>
        @lang('pages.exchanges.type.agreggators')
    </x-general.dropdown.list-item>
</x-exchanges.dropdown>
