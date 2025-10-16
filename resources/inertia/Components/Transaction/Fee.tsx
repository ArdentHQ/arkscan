import { ITransaction } from "@/types";
import { weiToArk } from "@/utils/UnitConverter";
import { BigNumber } from "bignumber.js";
import AmountFiatTooltip from "../General/AmountFiatTooltip";

// export default function Fee({
//     transaction,
//     className = '',
// }: {
//     transaction: ITransaction;
//     className?: string;
// }) {
//     const gasPrice = BigNumber(transaction.gas_price);
//     const gasUsed = BigNumber(transaction.gas_used);
//     const value = weiToArk(gasPrice.multipliedBy(gasUsed));

//     return (
//         <span>{value}</span>
//     );
// }

export default function Fee({
    transaction,
    className = '',
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
