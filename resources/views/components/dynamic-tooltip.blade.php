@props([
    'tooltip',
])

<div
    class="min-w-0"
    @if ($tooltip !== null && $tooltip !== '')
        x-data="{
            init() {
                window.addEventListener('resize', () => this.throttledShowTooltip());

                this.dynamicallyShowTooltip();
            },
            throttleTimeout: null,
            throttledShowTooltip() {
                if (this.throttleTimeout !== null) {
                    clearTimeout(this.throttleTimeout);
                }

                this.throttleTimeout = setTimeout(() => {
                    this.dynamicallyShowTooltip();

                    this.throttleTimeout = null;
                }, 500);
            },
            dynamicallyShowTooltip() {
                const el = this.$root;
                const tippyTooltip = el._tippy;
                if (el._tippy === undefined) {
                    return;
                }

                const childEl = el.firstElementChild;

                if (childEl.offsetWidth < childEl.scrollWidth) {
                    tippyTooltip.enable();
                } else {
                    tippyTooltip.disable();
                }
            }
        }"
        data-tippy-content="{{$tooltip}}"
    @endif
>
    {{ $slot }}
</div>
