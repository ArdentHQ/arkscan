import useSharedData from "@/hooks/use-shared-data";
import { ITokenTransfer } from "@/types/generated";
import classNames from "classnames";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

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
            <div className="inline-block leading-4.25">
                <AmountFiatTooltip
                    amount={tokenTransfer.amount}
                    isReceived={true}
                    hideCurrency={hideCurrency}
                    currency={tokenTransfer.token.symbol}
                />
            </div>
        </div>
    );
}
