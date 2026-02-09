import { ITokenTransfer } from "@/types/generated";
import classNames from "classnames";
import AmountSmall from "../General/AmountSmall";

export default function Amount({
    tokenTransfer,
    breakpoint = "md-lg",
    hideCurrency = false,
    testId,
}: {
    tokenTransfer: ITokenTransfer;
    withCurrency?: boolean;
    breakpoint?: "md-lg" | "lg" | "xl";
    hideCurrency?: boolean;
    testId?: string;
}) {
    const containerBreakpointClass = (
        {
            "md-lg": "md-lg:space-y-0",
            lg: "lg:space-y-0",
            xl: "xl:space-y-0",
        } as Record<string, string>
    )[breakpoint];

    return (
        <div
            className={classNames({
                "flex flex-col md:space-y-1": true,
                [containerBreakpointClass]: true,
            })}
            data-testid={testId}
        >
            <div className="inline-block space-x-1 leading-4.25">
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                    <AmountSmall amount={tokenTransfer.amount} hideTooltip hideCurrency={true} />
                </span>

                {!hideCurrency && <span>{tokenTransfer.token.symbol}</span>}
            </div>
        </div>
    );
}
