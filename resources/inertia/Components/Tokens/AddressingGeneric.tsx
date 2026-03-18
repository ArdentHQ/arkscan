import { ITokenAction } from "@/types/generated";
import { AddressingGeneric } from "@/Components/General/Addressing/AddressingGeneric";

export default function AddressingForTransfer({
    transfer,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transfer: ITokenAction;
}) {
    const sender = transfer.from;
    const recipient = transfer.to;
    const contractAddress =
        transfer.transaction!.deployed_contract_address ?? transfer.transaction!.to ?? recipient.address ?? "";

    return (
        <AddressingGeneric
            sender={sender.address}
            senderUsername={sender.username ?? undefined}
            recipient={recipient.address}
            recipientUsername={transfer.to.username}
            contractAddress={contractAddress}
            withTruncate={true}
            className={className}
            {...props}
        />
    );
}
