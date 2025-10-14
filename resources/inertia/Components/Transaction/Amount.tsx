import { useConfig } from "@/Providers/Config/ConfigContext";
import { ITransaction } from "@/types";
import { weiToArk } from "@/utils/UnitConverter";

export default function Amount({ transaction }: { transaction: ITransaction }) {
    const { network } = useConfig();

    const value = weiToArk(transaction.value, network!.currency, 2);

    return (
        <span>{value}</span>
    );
}
