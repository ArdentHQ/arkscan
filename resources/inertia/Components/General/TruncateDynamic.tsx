import { useEffect, useRef } from "react";

export default function TruncateDynamic({ value }: { value: string }) {
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!ref.current) {
            return;
        }

        let throttleTimeout: NodeJS.Timeout | null = null;

        const hasOverflow = (el: HTMLDivElement) => {
            return el.offsetWidth < el.scrollWidth;
        };

        const truncate = () => {
            if (!ref.current) {
                return;
            }

            ref.current.innerHTML = "";
            ref.current.appendChild(document.createTextNode(value));

            if (!hasOverflow(ref.current)) {
                return;
            }

            const baseLength = value.length;
            let length = baseLength;

            do {
                const a = value.substring(0, length);
                const b = value.substring(baseLength - length);
                const truncated = a + "..." + b;

                ref.current.innerHTML = "";
                ref.current.appendChild(document.createTextNode(truncated));

                length--;
            } while (hasOverflow(ref.current) && length >= 0);
        };

        const throttledTruncate = () => {
            if (throttleTimeout !== null) {
                clearTimeout(throttleTimeout);
            }

            throttleTimeout = setTimeout(() => {
                truncate();

                throttleTimeout = null;
            }, 50);
        };

        new ResizeObserver(throttledTruncate).observe(ref.current);

        window.addEventListener("resize", throttledTruncate);

        return () => {
            window.removeEventListener("resize", throttledTruncate);
        };
    }, [value, ref]);

    return (
        <div ref={ref} className="inline-flex w-full max-w-full overflow-hidden whitespace-nowrap">
            {value}
        </div>
    );
}
