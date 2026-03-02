import AmountFiatTooltip from "../General/AmountFiatTooltip";
import { Transaction } from "@/models/Transaction";
import useSettings from "@/Providers/Settings/useSettings";
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
    const { currency: selectedCurrency } = useSettings();
    const feeFiat = currency(transaction.feeFiat(selectedCurrency), selectedCurrency, true);

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
