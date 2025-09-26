import { ITransaction } from "@/types";
import { weiToArk } from "@/utils/UnitConverter";
import { BigNumber } from "bignumber.js";

export default function Fee({ transaction }: { transaction: ITransaction }) {
    const gasPrice = BigNumber(transaction.gas_price);
    const gasUsed = BigNumber(transaction.gas_used);
    const value = weiToArk(gasPrice.multipliedBy(gasUsed));

    return (
        <span>{value}</span>
    );
}
