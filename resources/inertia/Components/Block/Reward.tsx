import { Block } from "@/models/Block";
import AmountFiatTooltip from "../General/AmountFiatTooltip";
import useSettings from "@/Providers/Settings/useSettings";
import { currency } from "@/utils/number-formatter";

export default function Reward({
    block,
    withoutValue = true,
    withoutStyling = false,
    className = "",
}: {
    block: Block;
    withoutValue?: boolean;
    withoutStyling?: boolean;
    className?: string;
}) {
    const { currency: selectedCurrency } = useSettings();
    const totalRewardFiat = currency(block.totalRewardFiat(selectedCurrency), selectedCurrency);
    const rewardFiat = currency(block.rewardFiat(selectedCurrency), selectedCurrency);

    return (
        <div>
            <AmountFiatTooltip
                amount={block.totalReward}
                fiat={totalRewardFiat}
                className={className}
                withoutStyling={withoutStyling}
            />

            {!withoutValue && <div className="mt-1 text-xs font-semibold leading-4.25 lg:hidden">{rewardFiat}</div>}
        </div>
    );
}
