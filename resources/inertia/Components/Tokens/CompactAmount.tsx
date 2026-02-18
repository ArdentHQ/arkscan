import { formatCompact, networkCurrency } from "@/utils/number-formatter";
import AmountSmall from "@/Components/General/AmountSmall";
import Tooltip from "@/Components/General/Tooltip";

export default function CompactAmount({
    amount,
    tokenSymbol,
    hideSymbol = false,
}: {
    amount: number;
    tokenSymbol?: string;
    hideSymbol?: boolean;
}) {
    const { value, suffix } = formatCompact(amount);
    const isCompact = suffix !== undefined;

    const formattedFull = networkCurrency(amount, 8, false);

    const content = (
        <span className="inline-flex items-center space-x-1">
            <AmountSmall amount={value} hideTooltip hideCurrency suffix={suffix} />
            {!hideSymbol && tokenSymbol && <span>{tokenSymbol}</span>}
        </span>
    );

    if (isCompact) {
        return <Tooltip content={`${formattedFull} ${tokenSymbol ?? ""}`}>{content}</Tooltip>;
    }

    return content;
}
