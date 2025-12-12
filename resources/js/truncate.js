export const truncateMiddle = (text, maxLength = 10) => {
    if (typeof text !== "string") {
        return text;
    }

    const value = text.trim();
    if (value.length <= maxLength) {
        return value;
    }

    let partLength = Math.floor(maxLength / 2);

    let parts = [value.substring(0, partLength), value.substring(value.length - partLength)];

    return parts.join("…");
};

export const TruncateDynamic = (value) => {
    return Alpine.reactive({
        value,
        throttleTimeout: null,

        init() {
            new ResizeObserver(() => this.throttledTruncate()).observe(this.$root);

            window.addEventListener("resize", () => this.throttledTruncate());

            this.truncate();
        },

        throttledTruncate() {
            if (this.throttleTimeout !== null) {
                clearTimeout(this.throttleTimeout);
            }

            this.throttleTimeout = setTimeout(() => {
                this.truncate();

                this.throttleTimeout = null;
            }, 50);
        },

        async truncate() {
            const el = this.$root;

            let truncateValue = this.value;
            if (!truncateValue) {
                truncateValue = await Promise.resolve(this.truncateValue());
            }

            el.innerHTML = "";
            el.appendChild(document.createTextNode(truncateValue));

            if (!this.hasOverflow(el)) {
                return;
            }

            let length = truncateValue.length;
            do {
                const a = truncateValue.substr(0, length);
                const b = truncateValue.substr(-length);
                const truncated = a + "..." + b;

                el.innerHTML = "";
                el.appendChild(document.createTextNode(truncated));

                length--;
            } while (this.hasOverflow(el) && length >= 0);
        },

        hasOverflow(el) {
            return el.offsetWidth < el.scrollWidth;
        },
    });
};
