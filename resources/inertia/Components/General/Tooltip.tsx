import Tippy, { TippyProps } from "@tippyjs/react";
import classNames from "classnames";
import React, { useEffect, useMemo, useRef, useState } from "react";

type TooltipProps = TippyProps &
    React.PropsWithChildren & {
        dynamic?: boolean;
    };

function isTruncated(element: HTMLElement | null): boolean {
    if (!element) {
        return false;
    }

    return element.offsetWidth < element.scrollWidth;
}

export default function Tooltip({ children, dynamic = false, className, ...props }: TooltipProps) {
    const wrapperRef = useRef<HTMLDivElement | null>(null);
    const [shouldEnableTooltip, setShouldEnableTooltip] = useState(false);

    const throttledRecalc = useMemo(() => {
        let timeout: number | null = null;

        const recalc = () => {
            if (timeout !== null) {
                window.clearTimeout(timeout);
            }

            timeout = window.setTimeout(() => {
                setShouldEnableTooltip(isTruncated(wrapperRef.current));
                timeout = null;
            }, 500);
        };

        return recalc;
    }, []);

    useEffect(() => {
        if (!dynamic) {
            return;
        }

        throttledRecalc();

        window.addEventListener("resize", throttledRecalc);
        return () => window.removeEventListener("resize", throttledRecalc);
    }, [dynamic, throttledRecalc]);

    useEffect(() => {
        if (!dynamic) {
            return;
        }

        throttledRecalc();
    }, [children, dynamic, throttledRecalc]);

    const disabled = props.disabled || (dynamic ? !shouldEnableTooltip : false);

    return (
        <Tippy theme="ark" {...props} disabled={disabled}>
            <div ref={wrapperRef} className={classNames("tooltip-content", className)}>
                {children}
            </div>
        </Tippy>
    );
}
