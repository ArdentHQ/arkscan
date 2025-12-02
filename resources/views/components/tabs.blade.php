@props([
    'options',
    'default',
])

<div x-cloak>
    <x-tabs.inline-wrapper
        x-data="{
            init: function () {
                this.$watch('tab', () => {
                    this.selected = this.tab;
                });
            },
        }"
        class="sm:mb-3 sm:inline-flex"
        :default-selected="$default"
        on-selected="function (value) {
            this.tab = value;
        }"
    >
        @foreach ($options as $name => $text)
            <x-tabs.inline-tab
                :name="$name"
                :first="$loop->first"
            >
                <span>{{ $text }}</span>
            </x-tabs.inline-tab>
        @endforeach
    </x-tabs.inline-wrapper>
</div>
