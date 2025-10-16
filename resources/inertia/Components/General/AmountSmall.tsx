import { useConfig } from "@/Providers/Config/ConfigContext";
import { currencyWithDecimals } from "@/utils/number-formatter";
import Tippy from "@tippyjs/react";

function AmountSmallWithoutTooltip({
    amount,
    smallAmount,
}: {
    amount: number;
    smallAmount: number;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <>&lt;{smallAmount} {network!.currency}</>
            ) : (
                <>{currencyWithDecimals(amount, network!.currency, 2)}</>
            )}
        </>
    )
}

function AmountSmallWithTooltip({
    amount,
    smallAmount,
}: {
    amount: number;
    smallAmount: number;
}) {
    const { network } = useConfig();

    return (
        <>
            {amount < smallAmount ? (
                <Tippy content={currencyWithDecimals(amount, network!.currency, 18)}>
                    <span>
                        &lt;{smallAmount} {network!.currency}
                    </span>
                </Tippy>
            ) : (
                <Tippy content={currencyWithDecimals(amount, network!.currency, 18)}>
                    <span>
                        {currencyWithDecimals(amount, network!.currency, 2)}
                    </span>
                </Tippy>
            )}
        </>
    )
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
                <span>
                    0 {network!.currency}
                </span>
            ) : (
                <>
                    {hideTooltip ? (
                        <AmountSmallWithoutTooltip
                            amount={amount}
                            smallAmount={smallAmount}
                        />
                    ) : (
                        <AmountSmallWithTooltip
                            amount={amount}
                            smallAmount={smallAmount}
                        />
                    )}
                </>
            )}
        </>
    );
}
