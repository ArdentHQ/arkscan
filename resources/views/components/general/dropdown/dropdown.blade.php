@props([
    'button',
    'content',
    'dropdownClass' => null,
])

<div
    x-data="{
        isOpen: false,

        open() {
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
        },

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },
    }"
    {{ $attributes->class('relative') }}
>
    <div
        {{ $button->attributes }}
        @unless ($button->attributes->get('disabled') === true)
            @click="toggle"
        @endunless
    >
        {{ $button }}
    </div>

    <div
        x-show="isOpen"
        @click.away="close"
        x-transition
        x-cloak
        {{ $content->attributes->class([
            'flex flex-col overflow-hidden absolute py-3 whitespace-nowrap bg-white rounded-xl dark:bg-theme-secondary-800 z-10 mt-1 shadow-toggle-dropdown',
            $dropdownClass,
        ]) }}
    >
        <div class="flex overflow-y-auto flex-col h-full custom-scroll">
            {{ $content }}
        </div>
    </div>
</div>
