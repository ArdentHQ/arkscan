import { useNetwork } from "@/Providers/Network/NetworkContext";
import { ITransaction } from "@/types";
import { weiToArk } from "@/utils/UnitConverter";

export default function Amount({ transaction }: { transaction: ITransaction }) {
    const { network } = useNetwork();

    const value = weiToArk(transaction.value, network.currency, 2);

    return (
        <span>{value}</span>
    );
}
