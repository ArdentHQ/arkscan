@props([
    'name',
    'first' => false,
])

<button
    type="button"
    class="flex relative items-center pr-6 space-x-6 cursor-pointer first:pl-4 last:pr-4 transition-default dark:hover:text-theme-secondary-200 hover:text-theme-secondary-900"
    @click="select('{{ $name }}')"
    @keydown.enter="select('{{ $name }}')"
    @keydown.space.prevent="select('{{ $name }}')"
    role="tab"
    id="tab-{{ $name }}"
    aria-controls="panel-{{ $name }}"
    wire:key="tab-{{ $name }}"
    @keydown.arrow-left="selectPrevTab"
    @keydown.arrow-right="selectNextTab"
    :tabindex="selected === '{{ $name }}' ? 0 : -1"
    :aria-selected="selected === '{{ $name }}'"
    {{ $attributes }}
>
    @unless ($first)
        <div class="w-px h-5 bg-theme-secondary-300 dark:bg-theme-dark-800"></div>
    @endunless

    <span
        class="block pt-4 pb-3 w-full h-full font-semibold whitespace-nowrap border-b-2"
        :class="{
            'border-transparent dark:text-theme-dark-200 ': selected !== '{{ $name }}',
            'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-dark-50 dim:border-theme-dark-blue-600': selected === '{{ $name }}',
        }"
    >{{ $slot }}</span>
</button>

