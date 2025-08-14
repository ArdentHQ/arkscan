import { useEffect, useRef, useState } from "react";

export default function TruncateDynamic({ value }: { value: string }) {
    const [truncatedValue, setTruncatedValue] = useState(value);
    const ref = useRef<HTMLElement>(null);
    const throttleTimeout = useRef<ReturnType<typeof setTimeout> | null>(null);

    const hasOverflow = () => {
        if (!ref.current) {
            return false;
        }

        return ref.current.offsetWidth < ref.current.scrollWidth;
    }

    const truncate = async () => {
        if (!ref.current) {
            return;
        }

        let truncateValue = value;

        setTruncatedValue(truncateValue);

        ref.current.innerHTML = "";
        ref.current.appendChild(document.createTextNode(truncateValue));

        if (!hasOverflow()) {
            return;
        }

        let length = truncateValue.length;
        do {
            const a = truncateValue.substring(0, length);
            const b = truncateValue.substring(-length);

            setTruncatedValue(a + "..." + b);

            length--;
        } while (hasOverflow() && length >= 0);
    }

    const throttledTruncate = () => {
        if (throttleTimeout.current !== null) {
            clearTimeout(throttleTimeout.current);
        }

        throttleTimeout.current = setTimeout(() => {
            truncate();
        }, 50);
    }

    useEffect(() => {
        if (!ref.current) {
            return;
        }

        new ResizeObserver(() => throttledTruncate()).observe(ref.current);

        window.addEventListener("resize", throttledTruncate);

        truncate();

        return () => {
            window.removeEventListener("resize", throttledTruncate);

            if (throttleTimeout.current !== null) {
                clearTimeout(throttleTimeout.current);
            }
        };
    }, [ref]);

    return (
        <span ref={ref}>
            {truncatedValue}
        </span>
    );
};
