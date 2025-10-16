import { useConfig } from "@/Providers/Config/ConfigContext";
import { currencyWithDecimals } from "@/utils/number-formatter";
import Tippy from "@tippyjs/react";

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

    if (hideTooltip) {
        if (amount === 0.00) {
            return (
                <>0 {network!.currency}</>
            );
        }

        if (amount < smallAmount) {
            return (
                <>&lt;{smallAmount} {network!.currency}</>
            );
        }

        return (
            <>{currencyWithDecimals(amount, network!.currency, 2)}</>
        );
    }

    if (amount === 0.00) {
        return (
            <span>
                0 {network!.currency}
            </span>
        );
    }

    if (amount < smallAmount) {
        return (
            <Tippy content={currencyWithDecimals(amount, network!.currency, 18)}>
                <span>
                    &lt;{smallAmount} {network!.currency}
                </span>
            </Tippy>
        );
    }

    return (
        <Tippy content={currencyWithDecimals(amount, network!.currency, 18)}>
            <span>
                {currencyWithDecimals(amount, network!.currency, 2)}
            </span>
        </Tippy>
    );
}
