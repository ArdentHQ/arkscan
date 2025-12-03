@props([
    'options',
    'default',
])

<div x-cloak>
    <x-tabs.wrapper
        x-data="{
            init: function () {
                this.$watch('tab', () => {
                    this.selected = this.tab;
                });
            },
        }"
        class="sm:inline-flex sm:mb-3"
        :default-selected="$default"
        on-selected="function (value) {
            this.tab = value;
        }"
    >
        @foreach ($options as $name => $text)
            <x-tabs.tab :name="$name">
                <span>{{ $text }}</span>
            </x-tabs.tab>
        @endforeach
    </x-tabs.wrapper>
</div>
