import { currencyWithDecimals, formatCompact, networkCurrency } from "@/utils/number-formatter";
import AmountSmall from "@/Components/General/AmountSmall";
import Tooltip from "@/Components/General/Tooltip";
import TokenSymbol from "./TokenSymbol";

/**
 * Note: "compact" does not mean truncated. It means that the number is formatted in a compact way, e.g. 1.2K instead of 1,200.
 */
export default function CompactAmount({
    amount,
    tokenSymbol,
    hideSymbol = false,
    fullTokenSymbol,
}: {
    amount: number | string;
    tokenSymbol?: string;
    hideSymbol?: boolean;
    fullTokenSymbol?: string;
}) {
    const { value, suffix } = formatCompact(amount);

    const formattedFull = networkCurrency(amount, 8, false);
    const formattedValue = currencyWithDecimals({ value, currency: fullTokenSymbol ?? "", hideCurrency: true });

    const innerContent = (
        <span className="inline-flex items-center space-x-1">
            <AmountSmall amount={value} hideTooltip hideCurrency suffix={suffix} />

            {!hideSymbol && <TokenSymbol tokenSymbol={tokenSymbol} />}
        </span>
    );

    if (formattedFull !== formattedValue) {
        return <Tooltip content={`${formattedFull} ${fullTokenSymbol ?? ""}`}>{innerContent}</Tooltip>;
    }

    return innerContent;
}
