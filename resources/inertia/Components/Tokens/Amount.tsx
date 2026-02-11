import { ITokenTransfer, IWallet } from "@/types/generated";
import classNames from "classnames";
import AmountSmall from "../General/AmountSmall";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

export default function Amount({
    tokenTransfer,
    breakpoint = "md-lg",
    hideCurrency = false,
    wallet,
    testId,
}: {
    tokenTransfer: ITokenTransfer;
    breakpoint?: "md-lg" | "lg" | "xl";
    hideCurrency?: boolean;
    wallet?: IWallet;
    testId?: string;
}) {
    let isReceived = wallet ? tokenTransfer.to.address === wallet.address : false;
    let isSent = wallet ? tokenTransfer.from.address === wallet.address : false;
    const isSentToSelf = wallet ? tokenTransfer.from.address === tokenTransfer.to.address : false;

    let amount = tokenTransfer.amount;

    if (isSentToSelf) {
        isReceived = false;
    }

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
                {wallet && (
                    <AmountFiatTooltip
                        amount={amount}
                        isSent={isSent}
                        isReceived={isReceived}
                        isSentToSelf={isSentToSelf}
                        hideCurrency={hideCurrency}
                    />
                )}

                {!wallet && (
                    <>
                        <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                            <AmountSmall amount={tokenTransfer.amount} hideTooltip hideCurrency={true} />
                        </span>

                        {!hideCurrency && <span>{tokenTransfer.token.symbol}</span>}
                    </>
                )}
            </div>
        </div>
    );
}
