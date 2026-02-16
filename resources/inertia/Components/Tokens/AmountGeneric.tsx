import classNames from "classnames";
import AmountSmall from "../General/AmountSmall";
import { formatCompact } from "@/utils/number-formatter";

export default function AmountGeneric({
    amount,
    breakpoint = "md-lg",
    testId,
}: {
    amount: number;
    breakpoint?: "md-lg" | "lg" | "xl";
    testId?: string;
}) {
    const containerBreakpointClass = (
        {
            "md-lg": "md-lg:space-y-0",
            lg: "lg:space-y-0",
            xl: "xl:space-y-0",
        } as Record<string, string>
    )[breakpoint];

    const { value, suffix } = formatCompact(amount);

    return (
        <div
            className={classNames({
                "flex flex-col md:space-y-1": true,
                [containerBreakpointClass]: true,
            })}
            data-testid={testId}
        >
            <div className="inline-block space-x-1 leading-4.25">
                <AmountSmall amount={value} hideTooltip hideCurrency={true} />

                {suffix && <span>{suffix}</span>}
            </div>
        </div>
    );
}
