import { useTranslation } from "react-i18next";
import { PageSection } from "@/Components/PageSection";
import { TransactionRecipient } from "@/Pages/Transaction.contracts";
import { weiToArk } from "@/utils/UnitConverter";
import CompactAmount from "@/Components/Tokens/CompactAmount";
import TransactionAddress from "./Address";
import TableHeader from "@/Components/Tables/Desktop/TableHeader";
import TableCell from "@/Components/Tables/Desktop/TableCell";

export default function TransactionRecipients({ recipients }: { recipients: TransactionRecipient[] }) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("general.recipients")} noBorder>
            <div className="overflow-hidden rounded-b-xl rounded-t-xl border border-theme-secondary-300 dark:border-theme-dark-700">
                <div className="table-container table-encapsulated encapsulated-table-header-gradient px-6">
                    <table>
                        <thead className="bg-theme-secondary-100 dark:bg-black">
                            <tr className="border-b-none text-sm font-semibold">
                                <TableHeader name="general.address" className="w-full text-left" />
                                <TableHeader name="general.transaction.amount" className="text-right" type="number" />
                            </tr>
                        </thead>
                        <tbody>
                            {recipients.map((recipient, index) => (
                                <tr className="text-sm font-semibold" key={`${recipient.address}-${recipient.amount}`}>
                                    <TableCell>
                                        <TransactionAddress
                                            address={recipient.address}
                                            testId={`transaction:recipient:${index}`}
                                        />
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <CompactAmount amount={Number(weiToArk(recipient.amount))} hideSymbol />
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
