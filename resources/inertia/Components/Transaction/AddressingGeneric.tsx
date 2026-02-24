import { AddressingGeneric } from "../General/Addressing/AddressingGeneric";
import { Transaction } from "@/models/Transaction";

export default function AddressingForTransaction({
    transaction,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transaction: Transaction;
}) {
    const sender = transaction.sender;
    const recipient = transaction.recipient;
    const contractAddress = transaction.deployed_contract_address ?? transaction.to ?? recipient?.address ?? "";

    return (
        <AddressingGeneric
            sender={sender!.address}
            senderUsername={sender?.hasUsername && sender?.username ? sender?.username : undefined}
            recipient={recipient!.address}
            recipientUsername={recipient?.hasUsername && recipient?.username ? recipient?.username : undefined}
            contractAddress={contractAddress}
            disableTooltip={!transaction.method.isTransfer}
            withTruncate={transaction.method.isTransfer || transaction.method.isTokenTransfer}
            className={className}
            {...props}
        />
    );
}
