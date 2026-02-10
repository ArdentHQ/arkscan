import { ITokenTransfer } from "@/types/generated";
import { AddressingGeneric } from "../General/Addressing/AddressingGeneric";

export default function AddressingForTransfer({
    transfer,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transfer: ITokenTransfer;
}) {
    const sender = transfer.transaction!.sender;
    const recipient = transfer.to;
    const contractAddress =
        transfer.transaction!.deployed_contract_address ?? transfer.transaction!.to ?? recipient ?? "";

    return (
        <AddressingGeneric
            sender={sender!.address}
            senderUsername={sender?.hasUsername && sender?.username ? sender?.username : undefined}
            recipient={recipient}
            recipientUsername={transfer.toUsername}
            contractAddress={contractAddress}
            withTruncate={true}
            className={className}
            {...props}
        />
    );
}
