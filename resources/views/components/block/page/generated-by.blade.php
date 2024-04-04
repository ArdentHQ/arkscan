@props(['block'])

<x-general.page-section.container :title="trans('pages.block.generated_by')">
    <x-block.page.section-detail.row :title="trans('pages.block.header.validator')">
        <x-general.page-section.data.validator :validator="$block->validator()" />
    </x-block.page.section-detail.row>
</x-general.page-section.container>
