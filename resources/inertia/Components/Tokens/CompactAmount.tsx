import { formatCompact, networkCurrency } from "@/utils/number-formatter";
import AmountSmall from "@/Components/General/AmountSmall";
import Tooltip from "@/Components/General/Tooltip";

export default function CompactAmount({
    amount,
    tokenSymbol,
    hideSymbol = false,
    showFullOnDesktop = false,
}: {
    amount: number | string;
    tokenSymbol?: string;
    hideSymbol?: boolean;
    showFullOnDesktop?: boolean;
}) {
    const { value, suffix } = formatCompact(amount);
    const isCompact = suffix !== undefined;

    const formattedFull = networkCurrency(amount, 8, false);

    const compactContent = (
        <span className="inline-flex items-center space-x-1">
            <AmountSmall amount={value} hideTooltip hideCurrency suffix={suffix} />
            {!hideSymbol && tokenSymbol && <span>&nbsp;{tokenSymbol}</span>}
        </span>
    );

    if (showFullOnDesktop && isCompact) {
        return (
            <>
                <span className="hidden md:inline">
                    <span className="break-all">
                        {formattedFull}
                        {!hideSymbol && tokenSymbol && <span> {tokenSymbol}</span>}
                    </span>
                </span>
                <span className="md:hidden">
                    <Tooltip content={`${formattedFull} ${tokenSymbol ?? ""}`}>{compactContent}</Tooltip>
                </span>
            </>
        );
    }

    if (isCompact) {
        return <Tooltip content={`${formattedFull} ${tokenSymbol ?? ""}`}>{compactContent}</Tooltip>;
    }

    return compactContent;
}
