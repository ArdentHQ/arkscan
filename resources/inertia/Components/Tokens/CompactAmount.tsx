import { formatCompact, networkCurrency } from "@/utils/number-formatter";
import AmountSmall from "@/Components/General/AmountSmall";
import Tooltip from "@/Components/General/Tooltip";
import TokenSymbol from "./TokenSymbol";

export default function CompactAmount({
    amount,
    tokenSymbol,
    hideSymbol = false,
    fullTokenSymbol,
    showFullOnDesktop = false,
}: {
    amount: number | string;
    tokenSymbol?: string;
    hideSymbol?: boolean;
    fullTokenSymbol?: string;
    showFullOnDesktop?: boolean;
}) {
    const { value, suffix } = formatCompact(amount);
    const isCompact = suffix !== undefined;

    const formattedFull = networkCurrency(amount, 8, false);

    const compactContent = (
        <span className="inline-flex items-center space-x-1">
            <AmountSmall amount={value} hideTooltip hideCurrency suffix={suffix} />
            {!hideSymbol && <TokenSymbol tokenSymbol={tokenSymbol} fullTokenSymbol={fullTokenSymbol} />}
        </span>
    );

    if (showFullOnDesktop && isCompact) {
        return (
            <>
                <span className="hidden md:inline">
                    <span className="break-all inline">
                        {formattedFull}
                        {!hideSymbol && <TokenSymbol tokenSymbol={tokenSymbol} fullTokenSymbol={fullTokenSymbol} />}
                    </span>
                </span>
                <span className="md:hidden">
                    <Tooltip content={`${formattedFull} ${fullTokenSymbol ?? ""}`}>{compactContent}</Tooltip>
                </span>
            </>
        );
    }

    if (isCompact) {
        return <Tooltip content={`${formattedFull} ${fullTokenSymbol ?? ""}`}>{compactContent}</Tooltip>;
    }

    return compactContent;
}
