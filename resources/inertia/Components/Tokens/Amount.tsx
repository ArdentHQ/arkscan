import { ITokenAction, IWallet } from "@/types/generated";
import classNames from "classnames";
import AmountSmall from "../General/AmountSmall";
import AmountFiatTooltip from "../General/AmountFiatTooltip";
import { formatCompact, networkCurrency } from "@/utils/number-formatter";
import Tooltip from "@/Components/General/Tooltip";
import TokenSymbol from "./TokenSymbol";

export default function Amount({
    tokenAction,
    breakpoint = "md-lg",
    hideCurrency = false,
    wallet,
    testId,
}: {
    tokenAction: ITokenAction;
    breakpoint?: "md-lg" | "lg" | "xl";
    hideCurrency?: boolean;
    wallet?: IWallet;
    testId?: string;
}) {
    let isReceived = wallet ? tokenAction.to.address === wallet.address : false;
    let isSent = wallet ? tokenAction.from.address === wallet.address : false;
    const isSentToSelf = wallet ? tokenAction.from.address === tokenAction.to.address : false;

    let amount = tokenAction.amount;

    const { value, suffix } = formatCompact(amount);
    const isCompact = suffix !== undefined;
    const fullFormatted = isCompact
        ? `${networkCurrency(amount, 8, false)} ${tokenAction.token.symbolFull ?? tokenAction.token.symbol}`
        : undefined;

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
            <div className="inline-block space-x-1 whitespace-nowrap leading-4.25">
                {wallet && (
                    <Tooltip content={fullFormatted} disabled={!isCompact}>
                        <AmountFiatTooltip
                            amount={value}
                            suffix={suffix}
                            isSent={isSent}
                            isReceived={isReceived}
                            isSentToSelf={isSentToSelf}
                            hideCurrency={hideCurrency}
                        />
                    </Tooltip>
                )}

                {!wallet && (
                    <>
                        {isCompact ? (
                            <Tooltip content={fullFormatted} className="inline">
                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                    <AmountSmall amount={value} hideTooltip hideCurrency={true} suffix={suffix} />
                                </span>
                            </Tooltip>
                        ) : (
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                <AmountSmall amount={value} hideTooltip hideCurrency={true} suffix={suffix} />
                            </span>
                        )}

                        {!hideCurrency && (
                            <TokenSymbol
                                tokenSymbol={tokenAction.token.symbol}
                                fullTokenSymbol={tokenAction.token.symbolFull}
                            />
                        )}
                    </>
                )}
            </div>
        </div>
    );
}
