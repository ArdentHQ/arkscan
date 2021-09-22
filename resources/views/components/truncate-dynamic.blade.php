<span
    x-data="{
        value: '{{ $slot }}',
        init() {
            new ResizeObserver(() => this.truncate()).observe(this.$el);

            window.addEventListener('resize', () => this.truncate());

            this.truncate();
        },
        truncate() {
            const el = this.$el;

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
            } while(this.hasOverflow(el))
        },
        hasOverflow(el) {
            return el.offsetWidth < el.scrollWidth;
        },
    }"
    x-init="init"
    class="inline-flex overflow-hidden w-full max-w-full whitespace-nowrap"
>{{ $slot }}</span>
