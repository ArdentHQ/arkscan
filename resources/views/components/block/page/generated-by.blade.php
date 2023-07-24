@props(['block'])

<x-general.page-section.container :title="trans('pages.block.generated_by')">
    <x-block.page.section-detail.row :title="trans('pages.block.header.delegate')">
        <x-general.page-section.data.delegate :delegate="$block->delegate()" />
    </x-block.page.section-detail.row>
</x-general.page-section.container>
