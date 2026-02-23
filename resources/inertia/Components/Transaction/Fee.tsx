import AmountFiatTooltip from "../General/AmountFiatTooltip";
import { Transaction } from "@/models/Transaction";

export default function Fee({
    transaction,
    className = "",
    withoutStyling = false,
    hideCurrency = false,
}: {
    transaction: Transaction;
    className?: string;
    withoutStyling?: boolean;
    hideCurrency?: boolean;
}) {
    return (
        <AmountFiatTooltip
            amount={transaction.fee}
            fiat={transaction.feeFiat}
            className={className}
            withoutStyling={withoutStyling}
            hideCurrency={hideCurrency}
        />
    );
}
