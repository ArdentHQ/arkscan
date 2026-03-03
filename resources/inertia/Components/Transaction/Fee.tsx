import AmountFiatTooltip from "../General/AmountFiatTooltip";
import { Transaction } from "@/models/Transaction";
import useSharedData from "@/hooks/use-shared-data";
import { currency } from "@/utils/number-formatter";

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
    const { settings } = useSharedData();
    const feeFiat = currency(transaction.feeFiat, settings!.currency, true);

    return (
        <AmountFiatTooltip
            amount={transaction.fee}
            fiat={feeFiat}
            className={className}
            withoutStyling={withoutStyling}
            hideCurrency={hideCurrency}
        />
    );
}
