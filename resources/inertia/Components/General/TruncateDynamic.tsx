import { useEffect, useRef } from "react";

export default function TruncateDynamic({ length = 10, children }: React.PropsWithChildren<{
    length?: number;
}>) {
    const ref = useRef<HTMLDivElement>(null);
    const value = children as string;

    useEffect(() => {
        if (!ref.current) {
            return;
        }

        let throttleTimeout: NodeJS.Timeout | null = null;

        const hasOverflow = (el: HTMLDivElement) => {
            return el.offsetWidth < el.scrollWidth;
        }

        const truncate = () => {
            if (!ref.current) {
                return;
            }

            ref.current.innerHTML = ''
            ref.current.appendChild(document.createTextNode(value));

            if (!hasOverflow(ref.current)) {
                return;
            }

            let length = value.length;
            do {
                const a = value.substring(0, length);
                const b = value.substring(-length);
                const truncated = a + '...' + b;

                ref.current.innerHTML = ''
                ref.current.appendChild(document.createTextNode(truncated));

                length--;
            } while(hasOverflow(ref.current) && length >= 0)
        }

        const throttledTruncate = () => {
            if (throttleTimeout !== null) {
                clearTimeout(throttleTimeout);
            }

            throttleTimeout = setTimeout(() => {
                truncate();

                throttleTimeout = null;
            }, 50);
        }

        new ResizeObserver(throttledTruncate).observe(ref.current);

        return () => {
            window.removeEventListener('resize', throttledTruncate);
        }
    }, [value, ref]);

    return (
        <div
            ref={ref}
            className="inline-flex overflow-hidden w-full max-w-full whitespace-nowrap"
        >
            {children}
        </div>
    )
}
