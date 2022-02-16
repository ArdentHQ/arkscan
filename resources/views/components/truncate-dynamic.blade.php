<span
    x-data="{
        value: '{{ $slot }}',
        init() {
            new ResizeObserver(() => this.throttledTruncate()).observe(this.$root);

            window.addEventListener('resize', () => this.throttledTruncate());

            this.truncate();
        },
        throttleTimeout: null,
        throttledTruncate() {
            if (this.throttleTimeout !== null) {
                clearTimeout(this.throttleTimeout);
            }

            this.throttleTimeout = setTimeout(() => {
                this.truncate();

                this.throttleTimeout = null;
            }, 50);
        },
        truncate() {
            const el = this.$root;

            el.innerHTML = ''
            el.appendChild(document.createTextNode(this.value));

            if (!this.hasOverflow(el)) {
                return;
            }

            let length = this.value.length;
            do {
                const a = this.value.substr(0, length);
                const b = this.value.substr(-length);
                const truncated = a + '...' + b;

                el.innerHTML = ''
                el.appendChild(document.createTextNode(truncated));

                length--;
            } while(this.hasOverflow(el) && length >= 0)
        },
        hasOverflow(el) {
            return el.offsetWidth < el.scrollWidth;
        },
    }"
    class="inline-flex overflow-hidden w-full max-w-full whitespace-nowrap"
>{{ $slot }}</span>
