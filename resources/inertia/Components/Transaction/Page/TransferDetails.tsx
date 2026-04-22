import { useTranslation } from "react-i18next";
import { PageSection } from "@/Components/PageSection";
import { UnitConverter } from "@arkecosystem/typescript-crypto";
import CompactAmount from "@/Components/Tokens/CompactAmount";
import TransactionAddress from "./Address";
import TableHeader from "@/Components/Tables/Desktop/TableHeader";
import TableCell from "@/Components/Tables/Desktop/TableCell";
import { ITransactionDetails } from "@/types/generated";

type BatchTransfer = ITransactionDetails["batchTokenTransfers"][number];

export default function TransferDetails({
    transfers,
    tokenSymbol,
    fullTokenSymbol,
}: {
    transfers: BatchTransfer[];
    tokenSymbol: string;
    fullTokenSymbol?: string;
}) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.transaction.transfer_details")} noBorder>
            <div
                id="transfer-details"
                className="border-theme-secondary-300 dark:border-theme-dark-700 overflow-hidden rounded-t-xl rounded-b-xl border"
            >
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead className="bg-theme-secondary-100 dark:bg-black">
                            <tr className="border-b-none text-sm font-semibold">
                                <TableHeader name="general.address" className="w-full text-left" />
                                <TableHeader name="general.transaction.amount" className="text-right" type="number" />
                            </tr>
                        </thead>
                        <tbody>
                            {transfers.map((transfer, index) => (
                                <tr
                                    className="text-sm font-semibold"
                                    key={`${transfer.recipient.address}-${transfer.amount}-${index}`}
                                >
                                    <TableCell>
                                        <TransactionAddress
                                            address={transfer.recipient.address}
                                            wallet={{
                                                address: transfer.recipient.address,
                                                username: transfer.recipient.username ?? null,
                                            }}
                                            testId={`transaction:transfer:${index}`}
                                        />
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <CompactAmount
                                            amount={UnitConverter.weiToArk(transfer.amount)}
                                            tokenSymbol={tokenSymbol}
                                            fullTokenSymbol={fullTokenSymbol}
                                        />
                                    </TableCell>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </PageSection>
    );
}
