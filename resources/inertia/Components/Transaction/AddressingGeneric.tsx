import { ITransaction } from "@/types/generated";
import { AddressingGeneric } from "../General/Addressing/AddressingGeneric";

export default function AddressingForTransaction({
    transaction,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transaction: ITransaction;
}) {
    const sender = transaction.sender;
    const recipient = transaction.recipient;
    const contractAddress = transaction.deployed_contract_address ?? transaction.to ?? recipient?.address ?? "";

    return (
        <AddressingGeneric
            sender={sender!.address}
            senderUsername={sender?.username ?? undefined}
            recipient={recipient!.address}
            recipientUsername={recipient?.username ?? undefined}
            contractAddress={contractAddress}
            disableTooltip={!transaction.isTransfer}
            withTruncate={transaction.isTransfer || transaction.isTokenTransfer}
            className={className}
            {...props}
        />
    );
}
