import { useTranslation } from "react-i18next";
import { PageSection } from "@/Components/PageSection";
import AmountSmall from "@/Components/General/AmountSmall";
import { weiToArk } from "@/utils/UnitConverter";
import { formatCompact } from "@/utils/number-formatter";
import TransactionAddress from "./Address";
import TableHeader from "@/Components/Tables/Desktop/TableHeader";
import TableCell from "@/Components/Tables/Desktop/TableCell";
import { ITransactionDetails } from "@/types/generated";

type BatchTransfer = ITransactionDetails["batchTokenTransfers"][number];

export default function TransferDetails({
    transfers,
    tokenSymbol,
}: {
    transfers: BatchTransfer[];
    tokenSymbol: string;
}) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.transaction.transfer_details")} noBorder>
            <div
                id="transfer-details"
                className="overflow-hidden rounded-b-xl rounded-t-xl border border-theme-secondary-300 dark:border-theme-dark-700"
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
                                    key={`${transfer.recipient}-${transfer.amount}-${index}`}
                                >
                                    <TableCell>
                                        <TransactionAddress
                                            address={transfer.recipient}
                                            wallet={{
                                                address: transfer.recipient,
                                                hasUsername: transfer.recipientHasUsername,
                                                username: transfer.recipientUsername,
                                            }}
                                            testId={`transaction:transfer:${index}`}
                                        />
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {(() => {
                                            const { value, suffix } = formatCompact(Number(weiToArk(transfer.amount)));
                                            return (
                                                <span className="inline-flex items-center space-x-1">
                                                    <AmountSmall
                                                        amount={value}
                                                        hideTooltip
                                                        hideCurrency
                                                        suffix={suffix}
                                                    />
                                                    <span className="whitespace-nowrap text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-300">
                                                        {tokenSymbol}
                                                    </span>
                                                </span>
                                            );
                                        })()}
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
