import { currencyWithDecimals } from "@/utils/number-formatter";
import Tooltip from "./Tooltip";
import useConfig from "@/hooks/use-config";

function AmountSmallWithoutTooltip({ amount, smallAmount }: { amount: number; smallAmount: number }) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <>
                    &lt;{smallAmount} {network!.currency}
                </>
            ) : (
                <>{currencyWithDecimals(amount, network!.currency, 2)}</>
            )}
        </>
    );
}

function AmountSmallWithTooltip({ amount, smallAmount }: { amount: number; smallAmount: number }) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <Tooltip content={currencyWithDecimals(amount, network!.currency, 18)}>
                    <span>
                        &lt;{smallAmount} {network!.currency}
                    </span>
                </Tooltip>
            ) : (
                <Tooltip content={currencyWithDecimals(amount, network!.currency, 18)}>
                    <span>{currencyWithDecimals(amount, network!.currency, 2)}</span>
                </Tooltip>
            )}
        </>
    );
}

export default function AmountSmall({
    amount,
    smallAmount = 0.0001,
    hideTooltip = false,
}: {
    amount: number;
    smallAmount?: number;
    hideTooltip?: boolean;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount === 0 ? (
                <span>0 {network!.currency}</span>
            ) : (
                <>
                    {hideTooltip ? (
                        <AmountSmallWithoutTooltip amount={amount} smallAmount={smallAmount} />
                    ) : (
                        <AmountSmallWithTooltip amount={amount} smallAmount={smallAmount} />
                    )}
                </>
            )}
        </>
    );
}
