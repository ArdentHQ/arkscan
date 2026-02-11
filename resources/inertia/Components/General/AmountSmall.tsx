import { currencyWithDecimals } from "@/utils/number-formatter";
import Tooltip from "./Tooltip";
import useSharedData from "@/hooks/use-shared-data";

function AmountSmallWithoutTooltip({
    amount,
    smallAmount,
    hideCurrency = false,
    currency,
}: {
    amount: number;
    smallAmount: number;
    hideCurrency?: boolean;
    currency?: string;
}) {
    const { network } = useSharedData();

    return (
        <>
            {amount < smallAmount ? (
                <>
                    &lt;{smallAmount} {hideCurrency ? "" : currency || network!.currency}
                </>
            ) : (
                <>{currencyWithDecimals({ value: amount, currency: currency || network!.currency, hideCurrency })}</>
            )}
        </>
    );
}

function AmountSmallWithTooltip({
    amount,
    smallAmount,
    hideCurrency = false,
    currency,
}: {
    amount: number;
    smallAmount: number;
    hideCurrency?: boolean;
    currency?: string;
}) {
    const { network } = useSharedData();

    return (
        <>
            {amount < smallAmount ? (
                <Tooltip
                    content={currencyWithDecimals({
                        value: amount,
                        currency: currency || network!.currency,
                        decimals: 18,
                        hideCurrency,
                    })}
                >
                    <span>
                        &lt;{smallAmount} {hideCurrency ? "" : currency || network!.currency}
                    </span>
                </Tooltip>
            ) : (
                <Tooltip
                    content={currencyWithDecimals({
                        value: amount,
                        currency: currency || network!.currency,
                        decimals: 18,
                        hideCurrency,
                    })}
                >
                    <span>
                        {currencyWithDecimals({
                            value: amount,
                            currency: currency || network!.currency,
                            decimals: 2,
                            hideCurrency,
                        })}
                    </span>
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
    currency,
}: {
    amount: number;
    smallAmount?: number;
    hideTooltip?: boolean;
    hideCurrency?: boolean;
    currency?: string;
}) {
    const { network } = useSharedData();

    return (
        <>
            {amount === 0 ? (
                <span>0.00{!hideCurrency ? " " + (currency || network!.currency) : ""}</span>
            ) : (
                <>
                    {hideTooltip ? (
                        <AmountSmallWithoutTooltip
                            amount={amount}
                            smallAmount={smallAmount}
                            hideCurrency={hideCurrency}
                            currency={currency}
                        />
                    ) : (
                        <AmountSmallWithTooltip
                            amount={amount}
                            smallAmount={smallAmount}
                            hideCurrency={hideCurrency}
                            currency={currency}
                        />
                    )}
                </>
            )}
        </>
    );
}
