import { ITransaction } from "@/types/generated";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

export default function Fee({
    transaction,
    className = "",
    withoutStyling = false,
}: {
    transaction: ITransaction;
    className?: string;
    withoutStyling?: boolean;
}) {
    return (
        <AmountFiatTooltip
            amount={transaction.fee}
            fiat={transaction.feeFiat}
            className={className}
            withoutStyling={withoutStyling}
        />
    );
}
