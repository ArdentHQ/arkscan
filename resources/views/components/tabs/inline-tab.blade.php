@props([
    'name',
    'first' => false,
])

<button
    type="button"
    class="flex items-center space-x-6 relative pr-4 first:pl-4 first:pr-6 cursor-pointer transition-default dark:hover:text-theme-secondary-200 hover:text-theme-secondary-900"
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
        <div class="w-px h-5 bg-theme-secondary-300 dark:bg-theme-secondary-800"></div>
    @endunless

    <span
        class="block pt-4 pb-3 w-full h-full font-semibold whitespace-nowrap border-b-2"
        :class="{
            'border-transparent dark:text-theme-secondary-500 ': selected !== '{{ $name }}',
            'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-secondary-200': selected === '{{ $name }}',
        }"
    >{{ $slot }}</span>
</button>

