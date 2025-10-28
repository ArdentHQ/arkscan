import { IBlock } from "@/types/generated";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

export default function Reward({
    block,
    withoutValue = true,
    withoutStyling = false,
    className = "",
}: {
    block: IBlock;
    withoutValue?: boolean;
    withoutStyling?: boolean;
    className?: string;
}) {
    return (
        <>
            <AmountFiatTooltip
                amount={block.totalReward}
                fiat={block.totalRewardFiat}
                className={className}
                withoutStyling={withoutStyling}
            />

            {!withoutValue && (
                <div className="mt-1 text-xs font-semibold leading-4.25 xl:hidden">{block.rewardFiat}</div>
            )}
        </>
    );
}
