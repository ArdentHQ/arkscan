import { currencyWithDecimals } from "@/utils/number-formatter";
import Tooltip from "./Tooltip";
import useConfig from "@/hooks/use-config";

function AmountSmallWithoutTooltip({
    amount,
    smallAmount,
    hideCurrency = false,
}: {
    amount: number;
    smallAmount: number;
    hideCurrency?: boolean;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <>
                    &lt;{smallAmount} {hideCurrency ? "" : network!.currency}
                </>
            ) : (
                <>{currencyWithDecimals(amount, network!.currency, 2, hideCurrency)}</>
            )}
        </>
    );
}

function AmountSmallWithTooltip({
    amount,
    smallAmount,
    hideCurrency = false,
}: {
    amount: number;
    smallAmount: number;
    hideCurrency?: boolean;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <Tooltip content={currencyWithDecimals(amount, network!.currency, 18, hideCurrency)}>
                    <span>
                        &lt;{smallAmount} {hideCurrency ? "" : network!.currency}
                    </span>
                </Tooltip>
            ) : (
                <Tooltip content={currencyWithDecimals(amount, network!.currency, 18, hideCurrency)}>
                    <span>{currencyWithDecimals(amount, network!.currency, 2, hideCurrency)}</span>
                </Tooltip>
            )}
        </>
    );
}

export default function AmountSmall({
    amount,
    smallAmount = 0.0001,
    hideTooltip = false,
    hideCurrency = false,
}: {
    amount: number;
    smallAmount?: number;
    hideTooltip?: boolean;
    hideCurrency?: boolean;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount === 0 ? (
                <span>0{!hideCurrency ? " " + network!.currency : ""}</span>
            ) : (
                <>
                    {hideTooltip ? (
                        <AmountSmallWithoutTooltip
                            amount={amount}
                            smallAmount={smallAmount}
                            hideCurrency={hideCurrency}
                        />
                    ) : (
                        <AmountSmallWithTooltip amount={amount} smallAmount={smallAmount} hideCurrency={hideCurrency} />
                    )}
                </>
            )}
        </>
    );
}
